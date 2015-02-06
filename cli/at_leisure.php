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

// Import our base real estate cli bit
jimport('frenchconnections.cli.import');

require_once(__DIR__ . '/leisure/codebase/classes/belvilla_jsonrpc_curl_gz.class.php');

class AtLeisure extends Import
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
    // Add the classification table so we can get the location details
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');

    $params = array(
        "HouseCodes" => $acco_chunk,
        "Items" => array("BasicInformationV3",
            "MediaV2",
            "PropertiesV1",
            "LanguagePackENV4",
            "LayoutExtendedV2",
            "DistancesV1")
    );
    // Get a db instance and start a transaction
    $db = JFactory::getDbo();

    $user = JFactory::getUser('atleisure')->id;
    $rpc = new belvilla_jsonrpcCall('glynis', 'gironde');

    $this->out('About to get houses...');

    $rpc->makeCall('ListOfHousesV1');

    $houses = $rpc->getResult('json');

    $props = $this->getProps($houses);

    $this->out('Got houses...');

    // Chunk up the house codes baby!
    $accocode_chunks = array_chunk($props, 100);

    // Load up 100 property details at a time.
    foreach ($accocode_chunks as $chunk => $acco_chunk)
    {
      $this->out('Processing accommodation chunk ' . $chunk . '/' . count($accocode_chunks));

      $params['HouseCodes'] = $acco_chunk;

      $rpc->makeCall('DataOfHousesV1', $params);
      $result = $rpc->getResult("json");

      foreach ($result as $k => $acco)
      {
        try
        {      
          // Reset the data array
          $data = array();

          $this->out('About to process property ' . $acco->HouseCode . ' (' . $k . ' of ' . count($result) . ')');

          $db->transactionStart();

          // Check whether this property agency reference already exists in the versions table
          $id = $this->getPropertyVersion('#__property_versions', 'affiliate_property_id', $acco->HouseCode, $db);

          $this->out('Property version ID: ' . $id . ' for ' . $acco->HouseCode);

          // Only create new property stub if version ID not already existsing
          if (!$id)
          {
            $this->out('Adding property entry...');

            // Create an entry in the #__realestate_property table
            $property_id = $this->createProperty('#__property', $db, $user);
  
            $data['property']['property_id'] = (int) $property_id;

            $this->out('Created new property ID: ' . $property_id);
          }

          // Get the nearest city
          $city_id = $this->nearestcity($acco->BasicInformationV3->WGS84Latitude, $acco->BasicInformationV3->WGS84Longitude);

          // Get the location details for this property
          $classification = JTable::getInstance('Classification', 'ClassificationTable');
          $location = $classification->getPath($city_id);

          $data['property']['id'] = $id;
          $data['property']['affiliate_property_id'] = $acco->HouseCode;
          $data['property']['country'] = (int) $location[1]->id;
          $data['property']['area'] = (int) $location[2]->id;
          $data['property']['region'] = (int) $location[3]->id;
          $data['property']['department'] = (int) $location[4]->id;
          $data['property']['city'] = (int) $location[5]->id;
          $data['property']['latitude'] = $acco->BasicInformationV3->WGS84Latitude;
          $data['property']['longitude'] = $acco->BasicInformationV3->WGS84Longitude;
          $data['property']['created_by'] = $user; // TO DO get Allez Francais added to system - surpress renewal reminders
          $data['property']['created_on'] = $db->quote(JFactory::getDate());
          $data['property']['review'] = 0;
          $data['property']['published_on'] = $db->quote(JFactory::getDate());

          $data['unit']['id'] = $unit_id;

          $this->out('Saving property version...');

          // Save out the property version
          $table = JTable::getInstance('PropertyVersions', 'RentalTable');
          $table->set('_tbl_keys', array('id'));
          $property_version_id = $this->savePropertyVersion($table, $data['property']);

          $this->out('Working through images...');

          //Media
          if (isset($acco->MediaV2))
          {
            // Set a counter so we can order the pics we want to save
            $i = 1;

            foreach ($acco->MediaV2 as $media)
            {
              if ($media->Type == "Photos")
              {
                foreach ($media->TypeContents as $photo)
                {
                  foreach ($photo->Versions as $photoversion)
                  {

                    if ($photoversion->width == '750')
                    {

                      $url = $photoversion->URL;

                      // Save the image data out to the database...
                      $this->createImage($db, array($unit_version_id, $unit_id, '', $url, '', $i));

                      $i++;
                    }
                  }
                }
              }
            }
          }




          // Done so commit all the inserts and what have you...
          $db->transactionCommit();

          $this->out('Done processing... ' . $prop->agency_reference);
        }
        catch (Exception $e)
        {
          // Roll back any batched inserts etc
          $db->transactionRollback();

          var_dump($e);
          // Send an email, woot!
          //$this->email($e);
        }
        die;
      }
    }
  }

  private function getProps($acco_objs = array())
  {
    $props = array();

    if (empty($acco_objs))
    {
      return false;
    }

    foreach ($acco_objs as $acco_obj)
    {
      // Get only the FR houses
      if ($acco_obj->Country == 'FR')
      {
        array_push($props, $acco_obj->HouseCode);
      }
    }

    return $props;
  }

}

JApplicationCli::getInstance('AtLeisure')->execute();
