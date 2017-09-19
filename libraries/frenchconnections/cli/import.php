<?php

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
abstract class Import extends JApplicationCli
{

  public function parseFeed($uri = '', $parser = 'document')
  {
    // Fetch and parse the feed.
    // Throw exception if feed not parsed/available.
    // Import the document Feed parser.
    // The parser name is determined by the name of the root XML node...
    // not sure if this needs to be the case but appears to be the way it works...
    jimport('frenchconnections.feed.' . $parser);

    // Get an instance of JFeedFactory
    $feed = new JFeedFactory;

    // Register the parser, this bit that seems like overkill
    $feed->registerParser($parser, 'JFeedParser' . $parser);

    // Get and parse the feed, returns a parsed list of items.
    $data = $feed->getFeed($uri);

    return $data;
  }
  /*
   * Get the nearest town or city based on the town/city given and department
   */

  public function nearestcity($latitude = '', $longitude = '')
  {

    try
    {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->select("a.id, b.title");

      $query->from('#__classifications a');
      $query->innerjoin('#__classifications b on b.id = a.parent_id');
      $query->order('
        ( 3959 * acos(cos(radians(' . $latitude . ')) *
          cos(radians(a.latitude)) *
          cos(radians(a.longitude) - radians(' . $longitude . '))
          + sin(radians(' . $latitude . '))
          * sin(radians(a.latitude))))
        ');

      $db->setQuery($query, 0, 1);
      $rows = $db->loadObject();
    }
    catch (Exception $e)
    {
      throw new Exception('Problem getting nearest city - ' . $e->getMessage());
    }

    // If there's a nearest city then return it.
    if (!empty($rows))
    {
      return $rows->id;
    }

    return false;
  }

  /**
   * Gets a property version from the relevant property version table
   *
   * @param type $select The select field
   * @param type $table The table to select from
   * @param type $field The field to check against
   * @param type $agency_reference - The value of the above field
   * @param type $db
   * @return boolean
   * @throws Exception
   */
  public function getPropertyVersion($table = '', $field = '', $affiliate_reference = '', $db)
  {
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from($db->quoteName($table));
    $query->where($db->quoteName($field) . '=' . $db->quote($affiliate_reference));
    $query->where('review = 0');
    $db->setQuery($query);

    try
    {
      $row = $db->loadObject();
    }
    catch (Exception $e)
    {
      throw new Exception('Problem getting property version - ' . $e->getMessage());
    }

    // Check that we have a result.
    if (empty($row))
    {
      return false;
    }
    // Return the property version ID
    return $row;
  }

  /**
   * Relies on
   *
   * @param type $table
   * @param type $data
   * @return type
   * @throws Exception
   */
  public function load($table, $value)
  {

    try
    {
      $table->load($value);
    }
    catch (RuntimeException $e)
    {
      throw new Exception('Problem creating a new real estate property version in Allez Francais XML import createPropertyVersion()');
    }

    return $table;
  }

  /**
   * Relies on
   *
   * @param type $table
   * @param type $data
   * @return type
   * @throws Exception
   */
  public function save($table, $data = array())
  {
    try
    {
      $table->save($data);
    }
    catch (Exception $e)
    {
      throw new Exception('Problem saving data )' . $e->getMessage());
    }

    return $table;
  }


  public function email(Exception $e)
  {
    $mail = JFactory::getMailer();
    $mail->addRecipient('adamrifat@frenchconnections.co.uk');
    $mail->setBody($e->getMessage() . '<br />' . $e->getTraceAsString() . '<br />' . $e->getLine());
    $mail->setSubject($e->getMessage());
    $mail->isHtml(true);
    $mail->send();
  }

  public function getUnit()
  {
    return $this->unit;
  }

  public function getPropertyTypes()
  {
    return $this->property_types;
  }

  public function getLocationTypes()
  {
    return $this->location_types;
  }

  /**
    *
    * @param type $db
    * @param type $data
    * @return type
    * @throws Exception
    */
   public function createImage($db, $data)
   {
     $query = $db->getQuery(true);
     $query->insert('#__property_images_library')
             ->columns(
                     array(
                         $db->quoteName('version_id'), $db->quoteName('unit_id'),
                         $db->quoteName('image_file_name'), $db->quoteName('ordering')
                     )
             )
             ->values(implode(',', $data));
     $db->setQuery($query);
     try
     {
       $db->execute();
     }
     catch (RuntimeException $e)
     {
       throw new Exception($e->getMessage());
     }
     return $db->insertid();
   }

  /**
    * Get the images and save each into the database...
    * Should we generate thumbs and gallery images for each? Probably.
    *
    */
    public function getImages($db, $images, $unit_version_id, $property_id, $unit_id)
    {
      $i = 1;

      $model = JModelLegacy::getInstance('Image', 'RentalModel');

      foreach ($images as $image)
      {

        // Get the last two parts and implode it to make the name
        $image_name = $property_id . '-' . $i . '.jpg';

        // Check the property directory exists...
        if (!file_exists(JPATH_SITE . '/images/property/' . $unit_id))
        {
          JFolder::create(JPATH_SITE . '/images/property/' . $unit_id);
        }

        // The ultimate file path where we want to store the image
        $filepath = JPATH_SITE . '/images/property/' . $unit_id . '/' . $image_name;

        $uri = new JURI($image->url);
        $path = str_replace(' ', '%20', $uri->getPath());

        $uri->setPath($path);
        $uri->setQuery(false);


        if (!file_exists($filepath))
        {
          // Copy the image url directly to where we want it
          copy($uri->tostring(), $filepath);

          // Generate the profiles
          $model->generateImageProfile($filepath, (int) $unit_id, $image_name, 'gallery', 578, 435);
          $model->generateImageProfile($filepath, (int) $unit_id, $image_name, 'thumbs', 100, 100);
          $model->generateImageProfile($filepath, (int) $unit_id, $image_name, 'thumb', 210, 120);
        }

        $data = array($unit_version_id, $unit_id, $db->quote($image_name), $i);

        // Save the image data out to the database...
        $this->createImage($db, $data);


        $i++;

      }
    }

  public function _saveFacilities($facilities = array(), $unit_version_id, $unit_id)
  {
      $db = JFactory::getDBO();

      $query = $db->getQuery(true);

      $query->delete('#__unit_attributes')
              ->where('version_id = ' . (int) $unit_version_id);

      $db->setQuery($query);

      $db->execute();

      // Clear the query and start the insert
      $query->clear();
      $query->insert('#__unit_attributes');
      $query->columns('version_id,property_id,attribute_id');

      foreach ($facilities as $facility)
      {

          $insert = array();

          $insert[] = $unit_version_id;
          $insert[] = $unit_id;
          $insert[] = $facility;
          $query->values(implode(',', $insert));
      }

      $db->setQuery($query);
      $db->execute();

      return true;
  }


  public function _getFacilities($amenities = array())
  {

      $facilities = array();

      foreach($amenities as $key => $value)
      {
        if (array_key_exists($value, $this->facilities))
        {
          $facilities[] = $this->facilities[$value];
        }
      }

      return $facilities;
  }


}
