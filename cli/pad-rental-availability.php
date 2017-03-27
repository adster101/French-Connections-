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


// Register the Helloworld helper method
JLoader::register('RentalHelper', JPATH_ADMINISTRATOR . '/components/com_rental/helpers/rental.php');

JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class Renewals extends JApplicationCli
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

    $db = JFactory::getDBO();
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');

    // Get the tables for processing values into relevant tables
    $properties = $this->_getProps();

    // Get an instance of the unit model for saving the from price and availability last updated on
    $unit_model = JModelLegacy::getInstance('Unit', 'RentalModel');

    $availabilityTable = JTable::getInstance($type = 'Availability', $prefix = 'RentalTable', $config = array());

    foreach($properties as $key => $property)
    {
      // Get the existing availability for this property/unit
      $availability = $this->getAvailability($property->unit_id);

      // Pump this data into RentalHelper
      // Add this here to pad out the availability with availability periods
      $availability_by_day = RentalHelper::getAvailabilityByDay($availability);

      //
      $availabilityArr = RentalHelper::getAvailabilityByPeriod($availability_by_day);

      try
      {
        $db->transactionStart();

        $unit_id = $property->unit_id;

        $availabilityTable->reset();

        // Delete existing availability
        $availabilityTable->delete($unit_id);

        // Woot, put some availability back
        $availabilityTable->save($unit_id, $availabilityArr);

        // Done so commit all the inserts and what have you...
        $db->transactionCommit();

        $this->out('Done processing...' . $property->property_id);


      }
      catch (Exception $e)
      {
        // Roll back any batched inserts etc
        $db->transactionRollback();
        var_dump($e->getMessage());
      }
    }
  }
  /*
   * Get a list of properties due to expire and are set to manual renewal
   */

  private function _getProps($auto = false)
  {
    // Could just as easily be done with comma separated list as a param on the rental component
    $users_to_ignore = array();
    $users_to_ignore[] = JUser::getInstance('atleisure')->id;
    $users_to_ignore[] = JUser::getInstance('oliverstravels')->id;

    $db = JFactory::getDBO();
    /**
     * Get the date now
     */
    $date = JFactory::getDate();

    $query = $db->getQuery(true);

    $query->select('
      a.id as property_id,
      b.id as unit_id'
    );

    $query->from('#__property a');
    $query->join('left', '#__unit b on a.id = b.property_id');
    $query->where('expiry_date >= ' . $db->quote($date->calendar('Y-m-d')));
    $query->where('a.created_by not in (' . implode(',', $users_to_ignore) . ')');
    $query->where('b.published = 1');

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
  /**
   * Method to generate a query to get the availability for a particular property
   *
   * TO DO: Add a check to ensure that the user requesting the availability
   * is the owner...
   *
   * @param       int $id property id, not primary key in this case
   * @param       boolean $reset reset data
   * @return      boolean
   * @see JTable:load
   */
  public function getAvailability($id = null, $reset = true)
  {
    // Create a new query object.
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    $query = $db->getQuery(true);
    $query->select('unit_id, start_date, end_date, availability');
    $query->from($db->quoteName('#__availability'));
    $query->where($db->quoteName('unit_id') . ' = ' . $db->quote($id));
    $query->order('start_date');
    $db->setQuery($query);

    $db->setQuery($query);

    try {
      $rows = $db->loadObjectList();
    }
    catch (Exception $e) {
      $this->out('Problem getting availability for ...' . $id);
      return false;
    }

    return $rows;
  }
}

JApplicationCli::getInstance('Renewals')->execute();
