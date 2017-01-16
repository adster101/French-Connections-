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
jimport('joomla.filesystem.folder');

require_once(__DIR__ . '/leisure/codebase/classes/belvilla_jsonrpc_curl_gz.class.php');

class OliversTravels extends Import
{

    protected $property_types = array(160 => 1, 150 => 4, 60 => 5, 20 => 6, 30 => 7, 40 => 9, 70 => 10, 130 => 11, 100 => 11, 90 => 12, 140 => 9, 50 => 20);
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
    protected $region_maps = array();
    public $expiry_date;
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

      // Add the realestate property models
      JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');
      $tariffsTable = JTable::getInstance($type = 'Tariffs', $prefix = 'RentalTable');

      define('COM_IMAGE_BASE', JPATH_ROOT . '/images/property/');

      // Set a reasonable expiry date...
      $expiry_date = JFactory::getDate('+7 day')->calendar('Y-m-d');

      $date = JFactory::getDate()->calendar('Y-m-d');

      // Get DB instance
      $db = JFactory::getDbo();

      $user = JFactory::getUser('oliverstravels')->id;

      $this->out('About to get property list...');

      $properties = $this->getData('http://feeds.oliverstravels.com/v1/dwellings.json', $this->api_key);

      $property_list = json_decode($properties);

      $this->out('Got property list...');

      // Process
      foreach ($property_list->data as $property)
      {


        $property_data = $this->getData('http://feeds.oliverstravels.com/v1/dwellings/' . $property->id . '.json', $this->api_key);

        $property_data_json = json_decode($property_data);

        $propertyObj = $property_data_json->data[0];

        try
        {

          // We only need to look for French properties, at the moment...
          if ($propertyObj->address->country != 'France')
          {
            continue;
          }

          $this->out('Processing accommodation id ' . $propertyObj->id);

          $property_table = JTable::getInstance('Property', 'RentalTable');
          $unit_table = JTable::getInstance('Unit', 'RentalTable');

          $property_version_table = JTable::getInstance('PropertyVersions', 'RentalTable');
          $unit_version_table = JTable::getInstance('UnitVersions', 'RentalTable');

          $propertyURL = $this->getURL($propertyObj->urls[0]->url);

          // Reset the data array
          $data = array();

          $this->out('About to process property ' . $propertyObj->id);

          $db->transactionStart();

          // Check whether this affiliate property reference already exists in the versions table
          $this->property_version_detail = $property_version_table->load(array('affiliate_property_id' => $propertyObj->id), false);

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
              'published' => 1
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

            // Get the nearest city
            $city_id = $this->nearestcity($propertyObj->address->latitude, $propertyObj->address->longitude);

            // Get the location details for this property
            $classification = JTable::getInstance('Classification', 'ClassificationTable');

            $location = $classification->getPath($city_id);

            $data['property_version']['id'] = $property_version_table->id;
            $data['property_version']['property_id'] = $property_table->id;
            $data['property_version']['affiliate_property_id'] = $propertyObj->id;
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
            $data['property_version']['location_details'] = $propertyObj->descriptions->location_description;
            $data['property_version']['getting_there'] = $propertyObj->descriptions->getting_there;
            $data['property_version']['booking_url'] = 'http://scripts.affiliatefuture.com/AFClick.asp?affiliateID=340247&merchantID=6436&programmeID=20995&mediaID=0&tracking=&url=' . $propertyURL;

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
            if (!empty($propertyObj->descriptions->dwelling_description))
            {
              $data['unit_version']['description'] = '<p>' . strip_tags($propertyObj->descriptions->dwelling_description, '<p><br><b>') . '</p>';
            }

            if (!empty($propertyObj->descriptions->capacity_info))
            {
              $data['unit_version']['description'] .= '<p>' . strip_tags($propertyObj->descriptions->capacity_info, '<p><br><b>') . '</p>';
            }

            if (!empty($propertyObj->descriptions->interior_grounds))
            {
              $data['unit_version']['description'] .= '<p>' . strip_tags($propertyObj->descriptions->interior_grounds, '<p><br><b>') . '</p>';
            }

            if (!empty($propertyObj->descriptions->terms_and_conditions))
            {
              $data['unit_version']['description'] .= '<p>' . strip_tags($propertyObj->descriptions->terms_and_conditions, '<p><br><b>') . '</p>';
            }

            if (!empty($propertyObj->descriptions->special_offers))
            {
              $data['unit_version']['description'] .= '<p>' . strip_tags($propertyObj->descriptions->special_offers, '<p><br><b>') . '</p>';
            }

            // if (!empty($propertyObj->descriptions->catering_services))
            // {
            //   $data['unit_version']['description'] .= '<p>' . strip_tags($propertyObj->descriptions->catering_services) . '</p>';
            // }

            // etc ...

            $data['unit_version']['occupancy'] = $propertyObj->details->maximum_capacity;
            $data['unit_version']['changeover_day'] = 1521;
            $data['unit_version']['unit_title'] = addslashes($propertyObj->details->dwelling_name);
            $data['unit_version']['property_type'] = $this->getPropertyType($propertyObj->details->dwelling_type);
            $data['unit_version']['property_type'] = 11;
            $data['unit_version']['accommodation_type'] = 25;
            $data['unit_version']['additional_price_notes'] = $propertyObj->descriptions->rate_description;
            $data['unit_version']['bathrooms'] = $propertyObj->details->bathrooms;
            $data['unit_version']['base_currency'] = $propertyObj->details->currency;
            $data['unit_version']['bedrooms'] = $propertyObj->details->bedrooms;

            $unit_version_table->set('_tbl_keys', array('id'));

            $this->save($unit_version_table, $data['unit_version']);

            // Work out the facilities and save them against this unit and version
            $facilities = $this->_getFacilities($propertyObj->amenities);

            if (!empty($facilities))
            {
              $this->_saveFacilities($facilities, $unit_version_table->id, $unit_table->id);
            }

            $this->out('Working through images...');

            if (!$this->unit_version_detail)
            {
              $this->getImages($db, $propertyObj->photos, $unit_version_table->id, $property_table->id, $unit_table->id);
            }

            $this->out('Updating tariff info');

            $tariffs = $this->getTariffs($propertyObj->rates, $propertyObj->details->currency);

            $unit_id = $unit_table->id;

            // Set the pk to unit_id as we want to delete all tariffs for this property
            $tariffsTable->set('_tbl_keys', array('unit_id'));

            $tariffsTable->delete($unit_id);

            // Reset the table pk to id so we can insert new tariffs
            $tariffsTable->set('_tbl_keys', array('id'));

            foreach ($tariffs as $tariff)
            {

              $tariff['id'] = '';
              $tariff['unit_id'] = $unit_id;

              $tariffsTable->save($tariff);
            }

            // Done so commit all the inserts and what have you...
            $db->transactionCommit();

            $this->out('Done processing... ');
          }
          catch (Exception $e)
          {
            // Roll back any batched inserts etc
            $db->transactionRollback();

            // Send an email, woot!
            $this->email($e);
          }
      }
    }

    public function getTariffs($tariffs = array(), $base_currency = '')
    {

      $tariffsArr = array();

      foreach($tariffs as $key => $tariff)
      {
        $tariffsArr[$key]['start_date'] = $tariff->on_sale_from;

        if ($key > 0)
        {
          $tariffsArr[$key-1]['end_date'] = $tariff->on_sale_from;
        } else {
          $tariffsArr[$key]['end_date'] = '';
        }

        $priceArr = $this->_getPriceArray($tariff->weekly_price);

        $tariffsArr[$key]['tariff'] = $priceArr[$base_currency] / 100;

      }

      return $tariffsArr;
    }

    private function _getPriceArray($weeklyTariffs)
    {
      $prices = array();

      foreach ($weeklyTariffs as $key => $value)
      {
        $prices[$value->currency] = $value->price;
      }

      return $prices;
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

    private function _getFacilities($amenities = array())
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

    private function _saveFacilities($facilities = array(), $unit_version_id, $unit_id)
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



    public function getPropertyType($propertyObj)
    {

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

  public function getURL($url = '')
  {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_Setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    $response = curl_exec($ch);

    $info = curl_getinfo($ch);
    return $info['url'];
  }
}

JApplicationCli::getInstance('OliversTravels')->execute();
