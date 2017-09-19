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
if (file_exists(dirname(__DIR__) . '/defines.php')) {
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

// Import our base real estate cli bit
jimport('frenchconnections.cli.import');

class CDVillas extends Import {

  protected $facilities = array(
    'Fridge freezer' => 486,
    'Aga' => 737,
    'Oven' => 466,
    'Ceramic hob' => 464,
    'Tumble dryer' => 109,
    'Dishwasher' => 103,
    'Washing machine' => 110,
    'Microwave' => 108,
    'Roof terrace' => 329,
    'BBQ' => 474,
    'Garden' => 98,
    'Tennis court on site' => 75,
    'Private pool' => 100,
    'Shared pool' => 101,
    'Wi-Fi/Internet access' => 539,
    'Air conditioning' => 74,
    'Pets allowed' => 21,
    'Caretaker/owner lives on site' => 89,
    'Cable TV' => 3123,
    'DVD' => 80,
    'Indoor games' => 533,
    'Working fireplace' => 95,
    'Wheelchair Access' => 115);

    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */
  public function doExecute() {
      // Add the classification table so we can get the location details
      JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');
      JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');

      // Add the rental property models
      JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');

      define('COM_IMAGE_BASE', JPATH_ROOT . '/images/property/');

      // Set a reasonable expiry date...
      $expiry_date = JFactory::getDate('+365 day')->calendar('Y-m-d');

      $date = JFactory::getDate()->calendar('Y-m-d');

      // Get DB instance
      $db = JFactory::getDbo();

      // Get a db instance and start a transaction
      $db = JFactory::getDbo();
      $user = JFactory::getUser('enquiries@cdvillas.com')->id;

      $this->out('About to get feed...');

      // Get and parse out the feed
      $props = $this->parseFeed('http://www.cotedazurvillarentals.com/Xml/FConnect', 'villas');



      $this->out('Got feed...');



      $this->out('About to process feed results...');

      jimport('joomla.filesystem.folder');

      // Process
      foreach ($props->properties as $prop) {
      {

        try
        {

          $this->out('Processing accommodation id ' . $prop->affiliate_property_id);

          $property_table = JTable::getInstance('Property', 'RentalTable');
          $unit_table = JTable::getInstance('Unit', 'RentalTable');

          $property_version_table = JTable::getInstance('PropertyVersions', 'RentalTable');
          $unit_version_table = JTable::getInstance('UnitVersions', 'RentalTable');

          // Reset the data array
          $data = array();

          $db->transactionStart();

          // Check whether this affiliate property reference already exists in the versions table
          $this->property_version_detail = $property_version_table->load(array('affiliate_property_id' => $prop->affiliate_property_id), false);

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
              'is_bookable' => 0,
              'published' => 1
            );


            // Create an entry in the #__property table
            $property_detail = $this->save($property_table, $property);

            // Be aware that the table primary key is updated
              $this->out('Created new property ID: ' . $property_detail->id);

              // Get the nearest city
              $city_id = $this->nearestcity($prop->latitude, $prop->longitude);

              // Get the location details for this property
              $classification = JTable::getInstance('Classification', 'ClassificationTable');

              $location = $classification->getPath($city_id);

              $data['property_version']['id'] = $property_version_table->id;
              $data['property_version']['property_id'] = $property_table->id;
              $data['property_version']['affiliate_property_id'] = $prop->affiliate_property_id;
              $data['property_version']['country'] = (int) $location[1]->id;
              $data['property_version']['area'] = (int) $location[2]->id;
              $data['property_version']['region'] = (int) $location[3]->id;
              $data['property_version']['department'] = (int) $location[4]->id;
              $data['property_version']['city'] = (int) $location[5]->id;
              $data['property_version']['latitude'] = $prop->latitude;
              $data['property_version']['longitude'] = $prop->longitude;
              $data['property_version']['created_by'] = $user; // TO DO get Allez Francais added to system - surpress renewal reminders
              $data['property_version']['created_on'] = $db->quote($date);
              $data['property_version']['review'] = 0;
              $data['property_version']['published_on'] = $db->quote(JFactory::getDate());
              $data['property_version']['use_invoice_details'] = 1;
              $data['property_version']['location_details'] = $prop->location_details;
              $data['property_version']['getting_there'] = $prop->getting_there;
              $data['property_version']['booking_url'] = $prop->booking_url;

              $this->out('Saving property version details for ' . $property_table->id);

              // Set the table key back to version id. This ensures a new version is created
              // if there isn't one already
              $property_version_table->set('_tbl_keys', array('id'));

              $this->save($property_version_table, $data['property_version']);

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

              $data['unit_version']['id'] = $unit_version_table->id;
              $data['unit_version']['unit_id'] = $unit_detail->id;
              $data['unit_version']['property_id'] = $property_table->id;


              $data['unit_version']['description'] = '<p>' . $prop->description . '</p>';

              $data['unit_version']['occupancy'] = $prop->occupancy;
              $data['unit_version']['changeover_day'] = 1521;
              $data['unit_version']['unit_title'] = addslashes($prop->unit_title);
              $data['unit_version']['property_type'] = $prop->property_type;
              $data['unit_version']['accommodation_type'] = 25;
              $data['unit_version']['additional_price_notes'] = $prop->additional_price_notes;
              $data['unit_version']['bathrooms'] = $prop->bathrooms;
              $data['unit_version']['base_currency'] = $prop->base_currency;
              $data['unit_version']['single_bedrooms'] = $prop->single_bedrooms;
              $data['unit_version']['double_bedrooms'] = $prop->double_bedrooms;
              $data['unit_version']['triple_bedrooms'] = $prop->triple_bedrooms;
              $data['unit_version']['quad_bedrooms'] = $prop->quad_bedrooms;
              $data['unit_version']['twin_bedrooms'] = $prop->twin_bedrooms;
              $data['unit_version']['bedrooms'] = $prop->single_bedrooms + $prop->double_bedrooms + $prop->triple_bedrooms + $prop->quad_bedrooms + $prop->twin_bedrooms;

              $unit_version_table->set('_tbl_keys', array('id'));

              $this->save($unit_version_table, $data['unit_version']);

              // Work out the facilities and save them against this unit and version
              if (!empty($prop->facilities))
              {
                $this->_saveFacilities($prop->facilities, $unit_version_table->id, $unit_table->id);
              }

              $this->out('Working through images...');

              $this->getImages($db, $prop->images, $unit_version_table->id, $property_table->id, $unit_table->id);

            }
            else
            {
              // Load the unit details
              $unit_detail = $unit_table->load(array('property_id' => $property_table->id));
            }

            // Done so commit all the inserts and what have you...
            $db->transactionCommit();

            $this->out('Done processing... ');
          }
          catch (Exception $e)
          {
            var_dump($e->getMessage());die;
            // Roll back any batched inserts etc
            $db->transactionRollback();

            // Send an email, woot!
            $this->email($e);
          }

      }


    }
  }
}

JApplicationCli::getInstance('CDVillas')->execute();
