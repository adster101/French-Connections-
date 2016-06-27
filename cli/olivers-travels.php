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

// Import our base real estate cli bit
jimport('frenchconnections.cli.import');

require_once(__DIR__ . '/leisure/codebase/classes/belvilla_jsonrpc_curl_gz.class.php');

class OliversTravels extends Import
{

    public $bathroomnumbers = array(1031, 1042);
    public $bedroomnumbers = array(1028, 1030, 1031, 1032, 1033, 1035, 1038, 1040);
    protected $property_types = array(160 => 1, 150 => 4, 60 => 5, 20 => 6, 30 => 7, 40 => 9, 70 => 10, 130 => 11, 100 => 11, 90 => 12, 140 => 9, 50 => 20);
    protected $kitchen_facilities = array('Fridge freezer' => 486, 'Aga' => 737, 'Oven' => 466, 'Ceramic hob' => 464, 'Tumble dryer' => 109, 'Dishwasher' => 103, 'Washing machine' => 110, 'Microwave' => 108);
    protected $external_facilities = array('Roof terrace' => 329, 'BBQ' => 474, 'Garden' => 98, 'Tennis court' => 75);
    protected $internal_facilities = array();
    public $expiry_date;
    protected $location_types = array(360 => 133, 370 => 753, 380 => 135, 385 => 131);
    public $date;
    private $property_version_detail;
    private $unit_version_detail;

    public $api_key = 'f078696cef4c8976971f13b0bbf0e79d086ac8c6';


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

        // Set a reasonable expiry date...
        $expiry_date = JFactory::getDate('+7 day')->calendar('Y-m-d');

        $date = JFactory::getDate()->calendar('Y-m-d');

        // Get DB instance
        $db = JFactory::getDbo();

        $user = JFactory::getUser('atleisure')->id;

        $this->out('About to get property list...');

        $properties = $this->getData('http://feeds.oliverstravels.com/v1/dwellings.json', $this->api_key);

        $property_list = json_decode($properties);

        $this->out('Got property list...');

        // Process

        foreach ($property_list->data as $property)
        {

          $this->out('Processing accommodation id ' . $property->id);

          $property_data = $this->getData('http://feeds.oliverstravels.com/v1/dwellings/' . $property->id . '.json', $this->api_key);

          $property_data_json = json_decode($property_data);

          $propertyObj = $property_data_json->data[0];

          var_dump($property_data_json->data[0]->address->latitude);die;


              try
              {

                    $property_table = JTable::getInstance('Property', 'RentalTable');
                    $unit_table = JTable::getInstance('Unit', 'RentalTable');

                    $property_version_table = JTable::getInstance('PropertyVersions', 'RentalTable');
                    $unit_version_table = JTable::getInstance('UnitVersions', 'RentalTable');

                    // Reset the data array
                    $data = array();

                    $this->out('About to process property ' . $property->id);

                    $db->transactionStart();

                    // Check whether this affiliate property reference already exists in the versions table
                    $this->property_version_detail = $property_version_table->load(array('affiliate_property_id' => $property->id), false);

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
                            'is_bookable' => 1
                        );

                        // Create an entry in the #__property table
                        $property_detail = $this->save($property_table, $property);

                        // Be aware that the table primary key is updated
                        $this->out('Created new property ID: ' . $property_detail->id);
                    } else
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

                    // Get the nearest city
                    $city_id = $this->nearestcity($propertyObj->address->latitude, $propertyObj->address->longitude);

                    // Get the location details for this property
                    $classification = JTable::getInstance('Classification', 'ClassificationTable');

                    $location = $classification->getPath($city_id);

                    $data['property_version']['id'] = $property_version_table->id;
                    $data['property_version']['property_id'] = $property_table->id;
                    $data['property_version']['affiliate_property_id'] = $property->id;
                    $data['property_version']['country'] = (int) $location[1]->id;
                    $data['property_version']['area'] = (int) $location[2]->id;
                    $data['property_version']['region'] = (int) $location[3]->id;
                    $data['property_version']['department'] = (int) $location[4]->id;
                    $data['property_version']['city'] = (int) $location[5]->id;
                    $data['property_version']['latitude'] = $propertyObj->address->latitude;
                    $data['property_version']['longitude'] = $propertyObj->address->longitude;
                    $data['property_version']['created_by'] = $user; // TO DO get Allez Francais added to system - surpress renewal reminders
                    $data['property_version']['created_on'] = $db->quote($date);
                    $data['property_version']['review'] = 0;
                    $data['property_version']['published_on'] = $db->quote(JFactory::getDate());
                    $data['property_version']['use_invoice_details'] = 1;
                    $data['property_version']['location_details'] = $this->getDistances($propertyObj->descriptions->locationdescription);

                    // TO DO - See about adding nearby activities and access options if possible
                    // Likely append text field to description. Also, add languages spoken (e.g. English)

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
                    } else
                    {
                        // Load the unit details
                        $unit_detail = $unit_table->load(array('property_id' => $property_table->id));
                    }

                    $data['unit_version']['id'] = $unit_version_table->id;
                    $data['unit_version']['unit_id'] = $unit_detail->id;
                    $data['unit_version']['property_id'] = $property_table->id;

                    // Add the description, if there is one
                    if (!empty($propertyObj->descriptions->dwellingdescription))
                    {
                        $data['unit_version']['description'] = '<p>' . strip_tags($propertyObj->descriptions->dwellingdescription) . '</p>';
                    }



                    if (!empty($propertyObj->LanguagePackENV4->HouseOwnerTip))
                    {
                        $data['unit_version']['description'] .= '<p>' . $propertyObj->LanguagePackENV4->HouseOwnerTip . '</p>';
                    }

                    $data['unit_version']['occupancy'] = $propertyObj->BasicInformationV3->MaxNumberOfPersons;
                    $data['unit_version']['changeover_day'] = 445;
                    $data['unit_version']['unit_title'] = addslashes($propertyObj->BasicInformationV3->Name);
                    $data['unit_version']['property_type'] = $this->getPropertyType($propertyObj);
                    $data['unit_version']['accommodation_type'] = 25;
                    $data['unit_version']['additional_price_notes'] = $this->additionalCosts($propertyObj);
                    $data['unit_version']['bathrooms'] = $this->getBathrooms($propertyObj);
                    $data['unit_version']['base_currency'] = 'EUR';
                    $data['unit_version'] = $this->getBedrooms($layoutArr, $data['unit_version']);

                    $unit_version_table->set('_tbl_keys', array('id'));

                    $this->save($unit_version_table, $data['unit_version']);

                    $this->out('Working through images...');

                    // Work out the facilities and save them against this unit and version
                    $facilities = $this->_getFacilities($propertyObj, $layoutArr, $unit_version_table->id, $unit_table->id);

                    $this->_saveFacilities($facilities, $unit_version_table->id, $unit_table->id);

                    if (!$this->unit_version_detail)
                    {
                        // Woot
                        $this->getImages($db, $propertyObj, $unit_version_table->id, $unit_table->id);
                    }
                    // Done so commit all the inserts and what have you...
                    $db->transactionCommit();

                    $this->out('Done processing... ');
                } catch (Exception $e)
                {
                    // Roll back any batched inserts etc
                    $db->transactionRollback();

                    var_dump($e);
                    // Send an email, woot!
                    //$this->email($e);
                }

        }
    }

    // Wrapper function to get the feed data via CURL
    public function getData($uri = '', $api_key = '')
    {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $uri);

      // This is the important step
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Affiliate-Authentication: ' . $api_key));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $result = curl_exec($ch);

      curl_close($ch);

      return $result;
    }


    private function getProps($propertyObj_objs = array())
    {
        $props = array();

        if (empty($propertyObj_objs))
        {
            return false;
        }

        foreach ($propertyObj_objs as $propertyObj_obj)
        {
            // Get only the FR houses and the XX- test houses
            if ($propertyObj_obj->Country == 'FR' || (strpos($propertyObj_obj->HouseCode, 'XX') !== false))
            {
                array_push($props, $propertyObj_obj->HouseCode);
            }
        }

        return $props;
    }


    private function _getFacilities($propertyObj, $layout = array(), $unit_version_id = '', $unit_id = '')
    {

        $facilities = array();



        return $facilities;
    }

    private function _saveFacilities($facilities = array(), $unit_version_id, $unit_id)
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);

        $query->delete('#__unit_attributes')
                ->where('version_id = ' . (int) $unit_version_id);

        $db->setQuery($query);

        try
        {

            $db->execute();
        } catch (Exception $e)
        {

        }

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

        try
        {

            $db->execute();
        } catch (Exception $e)
        {
            var_dump($e);
            die;
        }

        return true;
    }

    private function getBathrooms($propertyObj)
    {

        $numberOfBathRooms = '';

        if (isset($propertyObj->LayoutExtendedV2))
        {
            foreach ($propertyObj->LayoutExtendedV2 as $layout)
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

    private function getBedrooms($layout = array(), $unit_version = array())
    {

        $unit_version['single_bedrooms'] = 0;
        $unit_version['double_bedrooms'] = 0;
        $unit_version['triple_bedrooms'] = 0;
        $unit_version['quad_bedrooms'] = 0;
        $unit_version['twin_bedrooms'] = 0;
        $unit_version['childrens_beds'] = 0;

        foreach ($layout as $rooms)
        {



        }

        return $unit_version;
    }

    public function getPropertyType($propertyObj)
    {

    }

    public function getLocationType($propertyObj)
    {

    }

    public function getImages($db, $propertyObj, $unit_version_id, $unit_id)
    {
    }


}

JApplicationCli::getInstance('OliversTravels')->execute();
