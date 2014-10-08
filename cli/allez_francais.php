<?php

/**
 * @package    Joomla.Cli
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// Initialize Joomla framework
        const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
  require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
  define('JPATH_BASE', dirname(__DIR__));
  require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class AllezFrancais extends JApplicationCli
{

  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */
  public function doExecute()
  {

    try
    {

      // Get and parse out the feed 
      $props = $this->parseFeed('http://www.allez-francais.com/allez-francais.xml');

      // Get a db instance and start a transaction
      $db = JFactory::getDbo();
      $db->transactionStart();

      // Add the realestate property models
      JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_realestate/models');
      $model = JModelLegacy::getInstance('Image', 'RealEstateModel');
      define('COM_IMAGE_BASE', JPATH_ROOT . '/images/property/');

      // Add the classification table so we can get the location details
      JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');

      // Loop over each of the $props returned from parseFeed above
      foreach ($props->properties as $prop)
      {

        // Check whether this property agency reference already exists in the versions table
        $exists = $this->getPropertyVersion($prop->agency_reference, $db);

        if (!$exists)
        {

          // Create an entry in the #__realestate_property table
          $property_id = $this->createProperty($db);

          // Get the location details for this property
          $classification = JTable::getInstance('Classification', 'ClassificationTable');
          $location = $classification->getPath($prop->city);

          $data = array();
          $data['realestate_property_id'] = $property_id;
          $data['agency_reference'] = $db->quote($prop->agency_reference);
          $data['title'] = $db->quote($prop->title);
          $data['country'] = (int) $location[1]->id;
          $data['area'] = (int) $location[2]->id;
          $data['region'] = (int) $location[3]->id;
          $data['department'] = (int) $location[4]->id;
          $data['city'] = (int) $location[5]->id;
          $data['latitude'] = $prop->latitude;
          $data['longitude'] = $prop->longitude;
          $data['created_by'] = 1; // TO DO get Allez Francais added to system - surpress renewal reminders
          $data['created_on'] = $db->quote(JFactory::getDate());
          $data['description'] = $db->quote($prop->description, true);
          $data['single_bedrooms'] = (int) $prop->single_bedrooms;
          $data['double_bedrooms'] = (int) $prop->double_bedrooms;
          $data['bathrooms'] = (int) $prop->bathrooms;
          $data['base_currency'] = $db->quote($prop->base_currency);
          $data['price'] = (int) $prop->price;
          $data['review'] = 0;
          $data['published_on'] = $db->quote(JFactory::getDate());

          $property_version_id = $this->createPropertyVersion($db, $data);

          foreach ($prop->images as $i => $image)
          {

            // Split the URL into an array
            $image_parts = explode('/', $image);
            
            // Get the last two parts and implode it to make the name
            $image_name = implode('-', array_slice($image_parts, -2, 2));
            
            // The ultimate file path where we want to store the image
            $filepath = JPATH_SITE . '/images/property/' . $property_id . '/' . $image_name;

            // Check the property directory exists...
            if (!file_exists(JPATH_SITE . '/images/property/' . $property_id))
            {
              jimport('joomla.filesystem.folder');
              JFolder::create(JPATH_SITE . '/images/property/' . $property_id);
            }


            if (!file_exists($filepath))
            {
              // Copy the image url directly to where we want it
              copy($image, $filepath);
              
              // Generate the profiles
              $model->generateImageProfile($filepath, (int) $property_id, $image_name, 'gallery', 578, 435);
              $model->generateImageProfile($filepath, (int) $property_id, $image_name, 'thumbs', 100, 100);
              $model->generateImageProfile($filepath, (int) $property_id, $image_name, 'thumb', 210, 120);
              
            }
            
            // Save the image data out to the database...
            $this->createImage($db, array($property_version_id, $property_id, $db->quote($image_name), $i));
            
          }
        }
        else
        {
          // Yep, we have it already!
          // $ref = $this->updateProperty($realestate_property_version->id);
        }
      }
    }
    catch (InvalidArgumentException $e)
    {
      $db->transactionRollback();
      $this->out(var_dump($e));
      // Set up exception email here.
      $mail = new JMail();
      $mail->AddAddress('adamrifat@frenchconnections.co.uk');
      $mail->Body($e->getMessage() . $e->getTraceAsString());
      $mail->Subject('Problem with AF property import');
      $send = $mail->send();
    }

    $db->transactionCommit();
    // Done
  }

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
      var_dump($e);
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
            ->values(implode(',',$data));

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
}

JApplicationCli::getInstance('AllezFrancais')->execute();
