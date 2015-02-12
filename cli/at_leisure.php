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

  public $bathroomnumbers = array(1031, 1042);
  public $bedroomnumbers = array(1028, 1030, 1031, 1032, 1033, 1035, 1038, 1040);
  protected $property_types = array(160 => 1, 150 => 4, 60 => 5, 20 => 6, 30 => 7, 40 => 9, 70 => 10, 130 => 11, 100 => 11, 90 => 12, 140 => 9, 50 => 20);
  public $expiry_date;
  protected $location_types = array(360 => 133, 370 => 753, 380 => 135, 385 => 131);
  public $date;
  private $property_version_detail;
  private $unit_version_detail;

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

    $expiry_date = JFactory::getDate('+1 week')->calendar('Y-m-d');
    $date = JFactory::getDate();

    // Get DB instance
    $db = JFactory::getDbo();

    $db->truncateTable('#__property');
    $db->truncateTable('#__property_versions');
    $db->truncateTable('#__unit');
    $db->truncateTable('#__unit_versions');
    $db->truncateTable('#__property_images_library');


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

          $layout = $this->getLayout($acco);

          $property_table = JTable::getInstance('Property', 'RentalTable');
          $unit_table = JTable::getInstance('Unit', 'RentalTable');

          $property_version_table = JTable::getInstance('PropertyVersions', 'RentalTable');
          $unit_version_table = JTable::getInstance('UnitVersions', 'RentalTable');

          // Reset the data array
          $data = array();

          $this->out('About to process property ' . $acco->HouseCode . ' (' . $k . ' of ' . count($result) . ')');

          $db->transactionStart();

          // Check whether this affiliate property reference already exists in the versions table
          $this->property_version_detail = $property_version_table->load(array('affiliate_property_id' => $acco->HouseCode), false);

          // Only create new property stub if version ID not already existsing
          if (!$this->property_version_detail)
          {
            $this->out('Property not found in versions table, adding property entry...');

            // Array of property details to create
            $property = array(
                'expiry_date' => $expiry_date, 'created_on' => $db->quote(JFactory::getDate()), 'review' => 0, 'created_by' => $user
            );

            // Create an entry in the #__property table
            $property_detail = $this->save($property_table, $property);

            // Be aware that the table primary key is updated 
            $this->out('Created new property ID: ' . $property_detail->id);
          }
          else
          {
            // Here we know we have the full property version detail
            $property_detail = $property_table->load($this->property_version_detail->property_id);
          }

          // Get the nearest city
          $city_id = $this->nearestcity($acco->BasicInformationV3->WGS84Latitude, $acco->BasicInformationV3->WGS84Longitude);

          // Get the location details for this property
          $classification = JTable::getInstance('Classification', 'ClassificationTable');
          $location = $classification->getPath($city_id);

          $data['property_version']['id'] = $this->property_version_detail->id;
          $data['property_version']['property_id'] = $property_detail->id;
          $data['property_version']['affiliate_property_id'] = $acco->HouseCode;
          $data['property_version']['country'] = (int) $location[1]->id;
          $data['property_version']['area'] = (int) $location[2]->id;
          $data['property_version']['region'] = (int) $location[3]->id;
          $data['property_version']['department'] = (int) $location[4]->id;
          $data['property_version']['city'] = (int) $location[5]->id;
          $data['property_version']['latitude'] = $acco->BasicInformationV3->WGS84Latitude;
          $data['property_version']['longitude'] = $acco->BasicInformationV3->WGS84Longitude;
          $data['property_version']['created_by'] = $user; // TO DO get Allez Francais added to system - surpress renewal reminders
          $data['property_version']['created_on'] = $db->quote(JFactory::getDate());
          $data['property_version']['review'] = 0;
          $data['property_version']['published_on'] = $db->quote(JFactory::getDate());
          $data['property_version']['use_invoice_details'] = 1;
          $data['property_version']['location_type'] = $this->getLocationType($acco);
          $data['property_version']['location_details'] = $this->getDistances($acco);

          // TO DO - See about adding nearby activities and access options if possible
          // Likely append text field to description. Also, add languages spoken (e.g. English)

          $this->out('Saving property version details for ' . $property_detail->id);

          // Set the table key back to version id. This ensures a new version is created 
          // if there isn't one already
          $property_version_table->set('_tbl_keys', array('id'));

          $this->property_version_detail = $this->save($property_version_table, $data['property_version']);

          // Same again, but this time for the unit...
          // Check whether this affiliate property reference already exists in the versions table
          $this->unit_version_detail = $unit_version_table->load(array('property_id' => $property_detail->id), false);

          // Only create new property stub if version ID not already existsing
          if (!$this->unit_version_detail)
          {
            $this->out('Unit not found in versions table, adding unit entry...');

            // Array of property details to create
            $unit = array('property_id' => $property_detail->id, 'created_by' => $user, 'published' => 1, 'ordering' => 1, 'review' => 0);

            // Create an entry in the #__property table
            $unit_detail = $this->save($unit_table, $unit);

            // Be aware that the table primary key is updated 
            $this->out('Created new unit ID: ' . $unit_detail->id);
          }
          else
          {
            // Load the unit details
            $unit_detail = $unit_table->load($this->unit_version_detail->unit_id);
          }

          $data['unit_version']['id'] = $this->unit_version_detail->id;
          $data['unit_version']['unit_id'] = $unit_detail->id;
          $data['unit_version']['property_id'] = $unit_detail->property_id;
          $data['unit_version']['description'] = '<p>' . $acco->LanguagePackENV4->Description . '</p>';
          $data['unit_version']['description'] .= '<p>' . $acco->LanguagePackENV4->HouseOwnerTip . '</p>';
          $data['unit_version']['description'] .= '<p>' . $acco->LanguagePackENV4->LayoutSimple . '</p>';
          $data['unit_version']['occupancy'] = $acco->BasicInformationV3->MaxNumberOfPersons;
          $data['unit_version']['unit_title'] = addslashes($acco->BasicInformationV3->Name);
          $data['unit_version']['property_type'] = $this->getPropertyType($acco);
          $data['unit_version']['accommodation_type'] = 25;

          $data['unit_version']['bathrooms'] = $this->getBathrooms($acco);

          $unit_version_table->set('_tbl_keys', array('id'));

          $unit_version_detail = $this->save($unit_version_table, $data['unit_version']);

          $this->out('Working through images...');
          $db->transactionCommit();

          $this->getImages($db, $acco, $unit_version_detail->id, $unit_detail->id);
          // Done so commit all the inserts and what have you...

          $this->out('Done processing... ');
        }
        catch (Exception $e)
        {
          // Roll back any batched inserts etc
          $db->transactionRollback();

          var_dump($e);
          // Send an email, woot!
          //$this->email($e);
        }
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

  private function getDistances($acco = '')
  {


    $distances = '';

    //DistancesV1
    if (isset($acco->DistancesV1))
    {
      $distances .= '<ul class="list list-unstyled">';

      foreach ($acco->DistancesV1 as $dist)
      {
        $distances.='<li>' . $dist->To . "'," . $dist->DistanceInKm . 'Km</li>';
      }
      $distances .= '</ul>';
    }

    return $distances;
  }

  private function getBathrooms($acco)
  {

    $numberOfBathRooms = '';

    if (isset($acco->LayoutExtendedV2))
    {
      foreach ($acco->LayoutExtendedV2 as $layout)
      {
        $item = $layout->Item;

        if (in_array($item, $this->bathroomnumbers))
        {
          $numberOfBathRooms += $layout->NumberOfItems;
        }
      }
    }

    return $numberOfBathRooms;
  }

  public function getPropertyType($acco)
  {
    $property_types = $this->getPropertyTypes();
    $property_type = '';
    //PropertiesV1
    if (isset($acco->PropertiesV1))
    {
      foreach ($acco->PropertiesV1 as $prop)
      {
        foreach ($prop->TypeContents as $TypeContent)
        {
          if (array_key_exists($TypeContent, $property_types))
          {
            $property_type = $property_types[$TypeContent];
          }
        }
      }
    }

    return $property_type;
  }

  public function getLocationType($acco)
  {
    $location_types = $this->getLocationTypes();
    $location_type = '';
    //PropertiesV1
    if (isset($acco->PropertiesV1))
    {
      foreach ($acco->PropertiesV1 as $prop)
      {
        foreach ($prop->TypeContents as $TypeContent)
        {
          if (array_key_exists($TypeContent, $location_types))
          {
            $location_type = $location_types[$TypeContent];
          }
        }
      }
    }

    return $location_type;
  }

  public function getImages($db, $acco, $unit_version_id, $unit_id)
  {
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

              if ($photoversion->Width == '750')
              {

                $url = $photoversion->URL;

                $data = array($unit_version_id, $unit_id, $db->quote($url), $i);

                // Save the image data out to the database...
                $this->createImage($db, $data);

                $i++;
              }
            }
          }
        }
      }
    }
  }

  public function getLayout($acco)
  {
    echo $acco->HouseCode;
    $layoutArr = array();

    if (isset($acco->LayoutExtendedV2))
    {
      foreach ($acco->LayoutExtendedV2 as $layout)
      {


        if (empty($layout->ParentItem) && !array_key_exists($layout->Item, $layoutArr))
        {
          $layoutArr[$layout->Item] = array();
        }

        if (!empty($layout->ParentItem) && array_key_exists($layout->ParentItem, $layoutArr))
        {
          $layoutArr[$layout->ParentItem][$layout->Item] = array();
        }

        foreach ($layoutArr as $key => $value)
        {
          if (!empty($layout->ParentItem) && array_key_exists($layout->ParentItem, $value))
          {
            $layoutArr[$key][$layout->ParentItem][] = $layout->Item;
          }
        }
      }
    }

    var_dump($layoutArr);
    die;
  }

}

JApplicationCli::getInstance('AtLeisure')->execute();