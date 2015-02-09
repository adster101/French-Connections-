<?php

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
abstract class Import extends JApplicationCli
{
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
        ( 3959 * acos(cos(radians(' . $longitude . ')) *
          cos(radians(a.latitude)) *
          cos(radians(a.longitude) - radians(' . $latitude . '))
          + sin(radians(' . $longitude . '))
          * sin(radians(a.latitude))) ) 
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
    $query->select('id');
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
    return $row->id;
  }

  /**
   * Creates new entry in $table 
   * 
   * @param type string - The table to create a new record in
   * @param type $db
   * @param type $user
   * @return type
   * @throws Exception
   */
  public function createProperty($table = '', $db, $user = 1, $published = 1)
  {
    $query = $db->getQuery(true);
    $expiry_date = JFactory::getDate('+1 week')->calendar('Y-m-d');
    $date = JFactory::getDate();

    $query->insert($db->quoteName($table))
            ->columns(
                    array(
                        $db->quoteName('expiry_date'), $db->quoteName('published'),
                        $db->quoteName('created_on'), $db->quoteName('review'),
                        $db->quoteName('created_by')
                    )
            )
            ->values($db->quote($expiry_date) . ', ' . (int) $published . ' , ' . $db->quote($date) . ',0,' . (int) $user);

    $db->setQuery($query);

    try
    {
      $db->execute();
    }
    catch (Exception $e)
    {
      throw new Exception('Problem creating a new real estate property in Allez Francais XML import createProperty()');
    }

    return $db->insertid();
  }

  /**
   * Relies on 
   * 
   * @param type $table
   * @param type $data
   * @return type
   * @throws Exception
   */
  public function savePropertyVersion($table, $data = array())
  {

    try
    {
      $table->save($data);
    }
    catch (RuntimeException $e)
    {
      throw new Exception('Problem creating a new real estate property version in Allez Francais XML import createPropertyVersion()');
    }

    return $table->id;
  }

  /**
   * TO DO - Make re-usable
   * 
   * @param type $db
   * @param type $data
   * @return type
   * @throws Exception
   */
  public function createImage($db, $data)
  {
    $query = $db->getQuery(true);

    $query->insert('#__realestate_property_images_library')
            ->columns(
                    array(
                        $db->quoteName('version_id'), $db->quoteName('realestate_property_id'),
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
      throw new Exception('Problem creating an image entry in the database for Allez Francais XML import createImage()');
    }

    return $db->insertid();
  }

  /**
   * TO DO - Make reusable
   * 
   * @param type $db
   * @param type $id
   * @throws Exception
   */
  public function updateProperty($db, $id)
  {
    $query = $db->getQuery(true);
    $expiry_date = JFactory::getDate('+1 week')->calendar('Y-m-d');
    $query->update('#__realestate_property')
            ->set('expiry_date = ' . $db->quote($expiry_date))
            ->where('id = ' . (int) $id);

    $db->setQuery($query);

    try
    {
      $db->execute();
    }
    catch (RuntimeException $e)
    {
      throw new Exception('Problem updating new real estate property in Allez Francais XML import updateProperty()');
    }
  }

  public function updatePropertyVersion($db, $data = array())
  {
    $query = $db->getQuery(true);

    $query->update('#__realestate_property_versions')
            ->set('title = ' . $data['title'] . ','
                    . 'description = ' . $data['description'] . ','
                    . 'single_bedrooms = ' . $data['single_bedrooms'] . ','
                    . 'double_bedrooms = ' . $data['double_bedrooms'] . ','
                    . 'base_currency = ' . $data['base_currency'] . ','
                    . 'price = ' . $data['price'] . ','
                    . 'published_on = ' . $data['published_on'])
            ->where('realestate_property_id = ' . $data['id']);

    $db->setQuery($query);

    try
    {
      $db->execute();
    }
    catch (RuntimeException $e)
    {
      throw new Exception('Problem updating real estate property version in Allez Francais XML import updatePropertyVersion()');
    }

    return $db->insertid();
  }

  public function email(Exception $e)
  {
    $mail = JFactory::getMailer();
    $mail->addRecipient('adamrifat@frenchconnections.co.uk');
    $mail->setBody($e->getMessage() . '<br />' . $e->getTraceAsString() . '<br />' . $e->getLine());
    $mail->setSubject($e->getMessage());
    $mail->isHtml(true);
    $send = $mail->send();
  }

}

