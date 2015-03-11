<?php

// Import our base Import
jimport('frenchconnections.cli.import');

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class RealestateImport extends Import
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

  /**
   * Creates new entry in $table 
   * 
   * @param type string - The table to create a new record in
   * @param type $db
   * @param type $user
   * @return type
   * @throws Exception
   */
  public function createProperty($db, $user = 1, $published = 1)
  {
    $query = $db->getQuery(true);
    $expiry_date = JFactory::getDate('+1 week')->calendar('Y-m-d');
    $date = JFactory::getDate();

    $query->insert($db->quoteName('#__realestate_property'))
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
   * TO DO - Remove these 'create' methods and use the base class methods.
   * @param type $db
   * @param type $data
   * @return type
   * @throws Exception
   */
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
      throw new Exception('Problem creating a new real estate property version in Allez Francais XML import createPropertyVersion()');
    }

    return $db->insertid();
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

    $query->insert('#__property_images_library')
            ->columns(
                    array(
                        $db->quoteName('version_id'), $db->quoteName('unit_id'),
                        $db->quoteName('caption'), $db->quoteName('ordering')
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
}
