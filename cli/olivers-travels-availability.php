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

require_once JPATH_BASE . '/administrator/components/com_rental/helpers/rental.php';

class OliversTravelsAvailability extends Import
{

  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */

   public $api_key = 'f078696cef4c8976971f13b0bbf0e79d086ac8c6';


  public function doExecute()
  {

    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');

    JLoader::import('frenchconnections.library');

    // Get DB instance
    $db = JFactory::getDbo();

    // Get the affiliate user id to use to pull out the properties which
    $user = JFactory::getUser('oliverstravels')->id;

    //
    $interval = new DateInterval('P1D');

    $properties = $this->_getProps();

    $this->out('Got houses...');

    // Get the tables for processing values into relevant tables
    $availabilityTable = JTable::getInstance($type = 'Availability', $prefix = 'RentalTable', $config = array());

    $tariffsTable = JTable::getInstance($type = 'Tariffs', $prefix = 'RentalTable');

    // Get an instance of the unit model for saving the from price and availability last updated on
    $unit_model = JModelLegacy::getInstance('Unit', 'RentalModel');

    // Load up 25 property details at a time.
    foreach ($properties as $property)
    {

      $availability = $this->getData('http://feeds.oliverstravels.com/v1/dwellings/' . $property->id . '/availability.json', $this->api_key);

      try
        {
          $availabilityArr = array();
          $availability = json_decode($availability);
          $counter = 1;

          foreach ($availability as $avper)
          {
            foreach ($avper as $period)
            {
              $availabilityObj = new stdClass;
              // Start date of the availability period
              $availabilityObj->start_date = JFactory::getDate($period->start_date)->calendar('d-m-Y');

              // Adjust the end date so it's the Friday rather than the following satrday
              $availabilityObj->end_date = JFactory::getDate($period->end_date)->calendar('d-m-Y');

              // Status is true, i.e. available
              $availabilityObj->availability = (int) $period->bookable;

              $availabilityArr[$counter] = $availabilityObj;

              $counter++;
            }
          }

          // Add this here to pad out the availability with availability periods
          $availability_by_day = RentalHelper::getAvailabilityByDay($availabilityArr);

          //
          $availabilityArr = RentalHelper::getAvailabilityByPeriod($availability_by_day);

          $db->transactionStart();

          $unit_id = $property->unit_id;

          $availabilityTable->reset();

          // Delete existing availability
          $availabilityTable->delete($unit_id);

          // Woot, put some availability back
          $availabilityTable->save($unit_id, $availabilityArr);

          // Set the data and save the from price against the unit
          $unit_data['availability_last_updated_on'] = JHtml::_('date', 'now', 'Y-m-d');
          $unit_data['id'] = $unit_id;

          if (!$unit_model->save($unit_data))
          {
            throw new Exception('Problem saving the unit from price/availability date');
          }

          // Done so commit all the inserts and what have you...
          $db->transactionCommit();


          $this->out('Done processing... ' . $property->id);
        }
        catch (Exception $e)
        {
          // Roll back any batched inserts etc
          $db->transactionRollback();

          var_dump($e->getMessage());
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
    /*
     * Get a list of properties due to expire and are set to manual renewal
     */

    private function _getProps($auto = false)
    {
      $user_id = JUser::getInstance('oliverstravels')->id;

      $db = JFactory::getDBO();
      /**
       * Get the date now
       */
      $date = JFactory::getDate();

      /*
       * Subtract one day from it so we also get the props that expired yesterday
       */
      $date->sub(new DateInterval('P1D'));

      $query = $db->getQuery(true);

      $query->select('b.affiliate_property_id as id, c.id as unit_id');

      $query->from('#__property a');
      $query->join('left', '#__property_versions b on a.id = b.property_id');
      $query->join('left', '#__unit c on a.id = c.property_id');
      $query->where('expiry_date >= ' . $db->quote($date->calendar('Y-m-d')));
      $query->where('a.created_by = ' . (int) $user_id);

      // echo $query->__toString();

      $db->setQuery($query);

      try {
        $rows = $db->loadObjectList();
      }
      catch (Exception $e) {
        $this->out('Problem getting props...');
        return false;
      }

      return $rows;
    }
}

JApplicationCli::getInstance('OliversTravelsAvailability')->execute();
