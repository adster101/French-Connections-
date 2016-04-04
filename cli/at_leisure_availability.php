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

    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');

    JLoader::import('frenchconnections.library');

    // Get DB instance
    $db = JFactory::getDbo();

    // Get the affiliate user id to use to pull out the properties which 
    $user = JFactory::getUser('atleisure')->id;

    $params = array(
        "HouseCodes" => $acco_chunk,
        "Items" => array("AvailabilityPeriodV1")
    );

    // 
    $interval = new DateInterval('P1D');

    $rpc = new belvilla_jsonrpcCall('glynis', 'gironde');

    $props = $this->getProps($user);
        
    $this->out('Got houses...');

    // Chunk up the house codes baby!
    $accocode_chunks = array_chunk(array_keys($props), 25);

    // Get the tables for processing values into relevant tables
    $availabilityTable = JTable::getInstance($type = 'Availability', $prefix = 'RentalTable', $config = array());

    $tariffsTable = JTable::getInstance($type = 'Tariffs', $prefix = 'RentalTable');

    // Get an instance of the unit model for saving the from price and availability last updated on
    $unit_model = JModelLegacy::getInstance('Unit', 'RentalModel');

    // Load up 25 property details at a time.
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

          $this->out('About to process property ' . $acco->HouseCode . ' (' . $k . ' of ' . count($result));
          $start_time = time();
          $availability = array();
          $tariffs = array();

          $counter = 1;
          foreach ($acco->AvailabilityPeriodV1 as $avper)
          {
            $ArrivalDate = DateTime::createFromFormat('Y-m-d', $avper->ArrivalDate);
            $DepartureDate = DateTime::createFromFormat('Y-m-d', $avper->DepartureDate);

            $arrival_day = JHtml::_('date', $avper->ArrivalDate, 'N');

            $nights = $DepartureDate->diff($ArrivalDate);
            $periodId = $this->__getPeriod($ArrivalDate, $nights->days);

            // Check that the available
            if ($periodId == '1w' && $arrival_day == 6)
            {
              // Start date of the availability period
              $availability[$counter]['start_date'] = JHtml::_('date', $avper->ArrivalDate, 'Y-m-d');

              // Adjust the end date so it's the Friday rather than the following satrday
              $availability[$counter]['end_date'] = JHtml::_('date', $DepartureDate->sub($interval)->format('Y-m-d'), 'Y-m-d');

              // Status is true, i.e. available
              $availability[$counter]['status'] = 1;

              // Sort out the tariffs as well
              if (!array_key_exists($avper->Price, $tariffs))
              {
                $tariffs[$avper->Price] = array();
                $tariffs[$avper->Price]['start_date'] = array();
                $tariffs[$avper->Price]['end_date'] = array();
              }

              if ($tariffs[$avper->Price]['start_date'] > $avper->ArrivalDate)
              {
                $tariffs[$avper->Price]['start_date'] = $avper->ArrivalDate;
                $tariffs[$avper->Price]['end_date'] = $avper->DepartureDate;
              }
              $counter++;
            }
          }

          $db->transactionStart();

          $unit_id = $props[$acco->HouseCode]->unit_id;

          $availabilityTable->reset();

          // Delete existing availability
          $availabilityTable->delete($unit_id);

          // Woot, put some availability back
          $availabilityTable->save($unit_id, $availability);
          
          // Set the pk to unit_id as we want to delete all tariffs for this property
          $tariffsTable->set('_tbl_keys', array('unit_id'));
          
          $tariffsTable->delete($unit_id);

          // Reset the table pk to id so we can insert new tariffs
          $tariffsTable->set('_tbl_keys', array('id'));

          
          foreach ($tariffs as $price => $dates)
          {
            $tariff = array();
            $tariff['id'] = '';
            $tariff['unit_id'] = $unit_id;
            $tariff['start_date'] = $dates['start_date'];
            $tariff['end_date'] = $dates['end_date'];
            $tariff['tariff'] = $price;

            $tariffsTable->save($tariff);
          }

          // Set the data and save the from price against the unit
          $unit_data['from_price'] = min(array_keys($tariffs));
          $unit_data['availability_last_updated_on'] = JHtml::_('date', 'now', 'Y-m-d');
          $unit_data['id'] = $unit_id;

          if (!$unit_model->save($unit_data))
          {
            throw new Exception('Problem saving the unit from price/availability date');
          }

          // Done so commit all the inserts and what have you...
          $db->transactionCommit();

          $end_time = time() - $start_time;
          $this->out('Done processing... ' . $end_time);
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
  }

  private function getAvailability()
  {
    
  }

  private function getProps($user = '')
  {

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('b.affiliate_property_id, c.id as unit_id')
            ->from('#__property a')
            ->leftJoin('#__property_versions b on a.id = b.property_id')
            ->leftJoin('#__unit c on c.property_id = b.property_id')
            ->where('a.created_by = ' . $user)
            ->where('a.published = 1');

    $db->setQuery($query);

    try
    {

      $props = $db->loadObjectList('affiliate_property_id');
    }
    catch (Exception $e)
    {
      throw new Exception($e->getMessage());
    }

    return $props;
  }

  /**
   * Private function __getPeriod
   * 
   * defines a periodid depending on arrivaldate and number of nights
   * 
   * @param	DateTime		the arrivaldate
   * @param	integer			the number of nights
   * @return 	string 
   */
  private function __getPeriod($a_date, $nights)
  {
    switch ($nights) {
      case 2:
        if ($a_date->format('N') == 5)
          return "wk"; //vrijdag
        break;
      case 3:
        if ($a_date->format('N') == 5)
          return "lw"; //vrijdag
        break;
      case 4:
        if ($a_date->format('N') == 1)
          return "mw"; //maandag
        break;
      case 7:
        return "1w";
        break;
      case 14:
        return "2w";
        break;
      case 21:
        return "3w";
        break;
      default:
        return "";
    }
  }

}

JApplicationCli::getInstance('AtLeisure')->execute();
