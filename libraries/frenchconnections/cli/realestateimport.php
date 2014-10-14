<?php

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class RealestateImport extends JApplicationCli
{

  public function parseFeed($uri = '')
  {
    // Fetch and parse the feed.
    // Throw exception if feed not parsed/available.
    // Import the document Feed parser.
    // This might get messy when we add the Freddy Rueda feed into the mix up.
    jimport('frenchconnections.feed.document');

    // Get an instance of JFeedFactory
    $feed = new JFeedFactory;

    // Register the parser, this bit that seems like overkill
    $feed->registerParser('document', 'JFeedParserDocument');

    // Get and parse the feed, returns a parsed list of items.
    $data = $feed->getFeed($uri);

    return $data;
  }

  public function getPropertyVersion($agency_reference = '', $db)
  {

    $query = $db->getQuery(true);
    $query->select('id');
    $query->from('#__realestate_property_versions');
    $query->where('agency_reference = ' . $db->quote((string) $agency_reference));
    $query->where('review = 0');
    $db->setQuery($query);

    try
    {
      $row = $db->loadObject();
    }
    catch (Exception $e)
    {
      throw new Exception('Problem getting property version in AF XML import line getPropertyVersion');
    }

    // Check that we have a result.
    if (empty($row))
    {
      return false;
    }

    // Return the property version ID
    return $row->id;
  }

  public function createProperty($db)
  {
    $query = $db->getQuery(true);
    $expiry_date = JFactory::getDate('+1 week')->calendar('Y-m-d');
    $date = JFactory::getDate();

    $query->insert('#__realestate_property')
            ->columns(
                    array(
                        $db->quoteName('expiry_date'), $db->quoteName('published'),
                        $db->quoteName('created_on'), $db->quoteName('review'),
                        $db->quoteName('created_by')
                    )
            )
            ->values($db->quote($expiry_date) . ', 1, ' . $db->quote($date) . ',1,1');

    $db->setQuery($query);

    try
    {
      $db->execute();
    }
    catch (RuntimeException $e)
    {
      throw new Exception('Problem creating a new real estate property in Allez Francais XML import createProperty()');
    }

    return $db->insertid();
  }

  public function createPropertyVersion($db, $data = array())
  {
    $query = $db->getQuery(true);
    $query->insert('#__realestate_property_versions')
            ->columns(
                    array(
                        $db->quoteName('realestate_property_id'), $db->quoteName('agency_reference'),
                        $db->quoteName('title'), $db->quoteName('country'),
                        $db->quoteName('area'), $db->quoteName('region'), $db->quoteName('department'),
                        $db->quoteName('city'), $db->quoteName('latitude'), $db->quoteName('longitude'),
                        $db->quoteName('created_by'), $db->quoteName('created_on'), $db->quoteName('description'),
                        $db->quoteName('single_bedrooms'), $db->quoteName('double_bedrooms'),
                        $db->quoteName('bathrooms'), $db->quoteName('base_currency'), $db->quoteName('price'),
                        $db->quoteName('review'), $db->quoteName('published_on')
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
      var_dump($query->dump());die;
      throw new Exception('Problem creating a new real estate property version in Allez Francais XML import createPropertyVersion()');
    }

    return $db->insertid();
  }

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
      var_dump($e);
      throw new Exception('Problem creating an image entry in the database for Allez Francais XML import createImage()');
    }

    return $db->insertid();
  }

  public function updateProperty($db, $id)
  {
    $query = $db->getQuery(true);
    $expiry_date = JFactory::getDate('+1 week')->calendar('Y-m-d');
    $date = JFactory::getDate();
   
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
      var_dump($e);
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
