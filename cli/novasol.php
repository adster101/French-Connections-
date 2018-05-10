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

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

require_once JPATH_BASE . '/administrator/components/com_rental/helpers/rental.php';


// Import our base real estate cli bit
jimport('frenchconnections.cli.import');
jimport('joomla.filesystem.folder');
// Import our base real estate cli bit
jimport('frenchconnections.cli.import');


class Novasol extends Import
{

  public $api_key = 'yII1NTvYnWpLQAK7D9X1G8j42f6LaS';

  // Numeric region codes - ignore property if not in one of these regions
  public $regions = array(139,141, 144, 147,137, 142,149, 145, 148);

  protected $facilities = array(
    '044' => 486, // Freezer
    '049' => 109, // Tumble drier
    '050' => 103, // Dishwasher
    '048' => 110, // Washing machine
    '047' => 108, // Microwave
    '016' => 474, // BBQ
    '011' => 98, // Garden
    'Private pool' => 100,
    'Shared pool' => 101,
    '111' => 539, // Wi-Fi/Internet access
    '009' => 74, //Air conditioning
    'Pets allowed' => 21,
    'Caretaker/owner lives on site' => 89,
    '112' => 3123, // Cable TV/english channels
    '057' => 80, // DVD
    '058' => 95, // Working fireplace
  );

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

    // Add the rental property models
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');
    $tariffsTable = JTable::getInstance($type = 'Tariffs', $prefix = 'RentalTable');

    $availabilityTable = JTable::getInstance($type = 'Availability', $prefix = 'RentalTable', $config = array());


    define('COM_IMAGE_BASE', JPATH_ROOT . '/images/property/');

    // Set a reasonable expiry date...
    $expiry_date = JFactory::getDate('+7 day')->calendar('Y-m-d');

    $date = JFactory::getDate()->calendar('Y-m-d');

    // Get DB instance
    $db = JFactory::getDbo();

    $user = JFactory::getUser('novasol')->id;

    $this->out('About to get property list...');

    // 1. Schedule a job to generate a batch file on the novasol server - this would be to pull out availability and tariffs
    // 2. Schedule a job for an hour later to process the above batch file (or to process a ping)
    // 3. Use this cli to periodically import any new properties

    // $properties = $this->getData('https://safe.novasol.com/api/batches?country=250&company=NOV&season=2017&replyTo=http://asdasd.co.uk', $this->api_key);
    // $properties = $this->getData('https://safe.novasol.com/api/batches/319101494945383051253', $this->api_key);
    // $properties = $this->getData('https://safe.novasol.com/api/translate?salesmarket=826', $this->api_key);

    // Get and parse out the feed
    $props = $this->parseFeed('http://dev.frenchconnections.co.uk/cli/novasol/products.xml', 'products');

    $this->out('Got property list...');
    // Process
    foreach ($props->properties as $propertyObj)
    {

      $this->out('Processing affiliate property id ' . $propertyObj->affiliate_property_id);

      $property_table = JTable::getInstance('Property', 'RentalTable');
      $unit_table = JTable::getInstance('Unit', 'RentalTable');

      $property_version_table = JTable::getInstance('PropertyVersions', 'RentalTable');
      $unit_version_table = JTable::getInstance('UnitVersions', 'RentalTable');

      $unit_model = JModelLegacy::getInstance('Unit', 'RentalModel');


      // Array to hold the 'data' for this listing
      $data = array();

      try
      {
        // Get the nearest city
        $city_id = $this->nearestcity($propertyObj->latitude, $propertyObj->longitude);

        // Get the location details for this property
        $classification = JTable::getInstance('Classification', 'ClassificationTable');

        $location = $classification->getPath($city_id);

        // Ignore properties not in the relevant regions
        if (!in_array($location[3]->id, $this->regions))
        {
          $db->transactionRollback();
          //$this->out('Skipping import of ' . $propertyObj->affiliate_property_id . ' not in a region of interest');

          continue;
        }

        // Start a transaction
        $db->transactionStart();

        // Check whether this affiliate property reference already exists in the versions table
        $this->property_version_detail = $property_version_table->load(array('affiliate_property_id' => $propertyObj->affiliate_property_id), false);

        // Only create new property stub if version ID not already existsing
        if (!$this->property_version_detail)
        {
          $this->out('Property not found in versions table, adding property entry...');

          // Array of property details to create
          $property = array(
            'expiry_date' => $expiry_date,
            'created_on' => $date,
            'review' => 0,
            'created_by' => $user,
            'is_bookable' => 1,
            'published' => 0
          );

          // Create an entry in the #__property table
          $property_detail = $this->save($property_table, $property);

          // Be aware that the table primary key is updated
            $this->out('Created new property ID: ' . $property_detail->id);
          }
          else
          {
            // Array of property details to create
            $property = array(
              'id' => $property_version_table->property_id,
              'expiry_date' => $expiry_date
            );

            // Update the property expiry date
            // N.B. If we wanted to amend the process to update on each import
            // this would be the place to do it.
            $this->save($property_table, $property);

            // Here we know we have the full property version detail
            $property_detail = $property_table->load($property_version_table->property_id);
          }



          $data['property_version']['id'] = $property_version_table->id;
          $data['property_version']['property_id'] = $property_table->id;
          $data['property_version']['affiliate_property_id'] = $propertyObj->affiliate_property_id;
          $data['property_version']['country'] = (int) $location[1]->id;
          $data['property_version']['area'] = (int) $location[2]->id;
          $data['property_version']['region'] = (int) $location[3]->id;
          $data['property_version']['department'] = (int) $location[4]->id;
          $data['property_version']['city'] = (int) $location[5]->id;
          $data['property_version']['latitude'] = $propertyObj->latitude;
          $data['property_version']['longitude'] = $propertyObj->longitude;
          $data['property_version']['created_by'] = $user; // TO DO get Allez Francais added to system - surpress renewal reminders
          $data['property_version']['created_on'] = $db->quote($date);
          $data['property_version']['review'] = 0;
          $data['property_version']['published_on'] = $db->quote(JFactory::getDate());
          $data['property_version']['use_invoice_details'] = 1;
          $data['property_version']['booking_url'] = $propertyObj->booking_url;

          $this->out('Saving property version details for ' . $property_table->id);

          // Set the table key back to version id. This ensures a new version is created
          // if there isn't one already
          $property_version_table->set('_tbl_keys', array('id'));

          $this->save($property_version_table, $data['property_version']);

          // Same again, but this time for the unit...
          // Check whether this affiliate property reference already exists in the versions table
          $this->unit_version_detail = $unit_version_table->load(array('property_id' => $property_table->id), false);

          // Only create new property stub if version ID not already existsing
          if (!$this->unit_version_detail)
          {
            $this->out('Unit not found in versions table, adding unit entry...');

            // Array of property details to create
            $unit = array('property_id' => $property_table->id, 'created_by' => $user, 'published' => 1, 'ordering' => 1, 'review' => 0);

            // Create an entry in the #__property table
            $unit_detail = $this->save($unit_table, $unit);

            // Be aware that the table primary key is updated
            $this->out('Created new unit ID: ' . $unit_detail->id);
          }
          else
          {
            // Load the unit details
            $unit_detail = $unit_table->load(array('property_id' => $property_table->id));
          }

          $data['unit_version']['id'] = $unit_version_table->id;
          $data['unit_version']['unit_id'] = $unit_detail->id;
          $data['unit_version']['property_id'] = $property_table->id;

          // Add the description, if there is one
          if (!empty($propertyObj->description))
          {
            $data['unit_version']['description'] = $propertyObj->description;
          }

          $data['unit_version']['occupancy'] = $propertyObj->occupancy;
          $data['unit_version']['changeover_day'] = 1521;
          $data['unit_version']['unit_title'] = $propertyObj->unit_title;
          $data['unit_version']['property_type'] = $propertyObj->property_type;
          $data['unit_version']['accommodation_type'] = 25;
          $data['unit_version']['bathrooms'] = $propertyObj->bathrooms;
          $data['unit_version']['base_currency'] = $propertyObj->base_currency;
          $data['unit_version']['single_bedrooms'] = $propertyObj->single_bedrooms;
          $data['unit_version']['double_bedrooms'] = $propertyObj->double_bedrooms;
          $data['unit_version']['twin_bedrooms'] = $propertyObj->twin_bedrooms;
          $data['unit_version']['triple_bedrooms'] = $propertyObj->triple_bedrooms;
          $data['unit_version']['bedrooms'] = $propertyObj->single_bedrooms + $propertyObj->double_bedrooms + $propertyObj->twin_bedrooms + $propertyObj->triple_bedrooms;

          $unit_version_table->set('_tbl_keys', array('id'));

          $this->save($unit_version_table, $data['unit_version']);

          if (!empty($propertyObj->facilities))
          {
            $this->_saveFacilities($propertyObj->facilities, $unit_version_table->id, $unit_table->id);
          }

          $this->out('Updating tariff info');

          $unit_id = $unit_table->id;

          // Set the pk to unit_id as we want to delete all tariffs for this property
          $tariffsTable->set('_tbl_keys', array('unit_id'));

          $tariffsTable->delete($unit_id);

          // Reset the table pk to id so we can insert new tariffs
          $tariffsTable->set('_tbl_keys', array('id'));

          foreach ($propertyObj->tariffs as $tariff)
          {

            $tariff['id'] = '';
            $tariff['unit_id'] = $unit_id;

            $tariffsTable->save($tariff);
          }

          $this->out('Working through images...');

          if (!$this->unit_version_detail)
          {
            $this->getImages($db, $propertyObj->images, $unit_version_table->id, $property_table->id, $unit_table->id);
          }


          // Update the availability
          $availabilityLength = strlen($propertyObj->availability);

          $availability_by_day = array();

          $DateInterval = new DateInterval('P1D');

          // Start date to populate the availability array.
          // This feed only supplies availability for a year...
          $date = new DateTime('01-01-2017');

          for ($i=1;$i<$availabilityLength;$i++) {

            // First day of year
            // Add one day to the start date for each day of availability
            $date = $date->add($DateInterval);

            // Add the day as an array key storing the availability status as the value
            if ($propertyObj->availability[$i] == 'A') {
              $availability_by_day[date_format($date, 'd-m-Y')] = 1;
            }
            else
            {
              $availability_by_day[date_format($date, 'd-m-Y')] = 0;
            }
          }

          $availabilityArr = RentalHelper::getAvailabilityByPeriod($availability_by_day);


          $availabilityTable->reset();

          // Delete existing availability
          $availabilityTable->delete($unit_id);

          // Woot, put some availability back
          $availabilityTable->save($unit_id, $availabilityArr);

          // Set the data and save the from price against the unit
          $unit_data['availability_last_updated_on'] = JFactory::getDate()->calendar('Y-m-d');

          $unit_data['id'] = $unit_id;

          if (!$unit_model->save($unit_data))
          {
            throw new Exception('Problem saving the unit from price/availability date');
          }

          // Done so commit all the inserts and what have you...
          $db->transactionCommit();

          $this->out('Done processing... ' . $propertyObj->affiliate_property_id);

        }
        catch (Exception $e)
        {
          // Roll back any batched inserts etc
          $db->transactionRollback();
          var_dump($e->getMessage());
        }
    }
  }

  // Wrapper function to get the feed data via CURL
  public function getData($uri = '', $api_key = '')
  {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Key: ' . $api_key));
    curl_setopt($ch, CURLOPT_URL, $uri);
    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
  }
}

JApplicationCli::getInstance('Novasol')->execute();
