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


/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class RandomSearchCron extends JApplicationCli
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

    try {

      // Get a list of all the units attached to live properties.
      $props = $this->_getProps();

      $db->transactionStart();

      $this->_clearSearchOrdering();

      foreach ($props as $order => $unit)
      {
        $query = $db->getQuery(true);

        $query->update('#__unit')
                ->set('search_ordering = ' . $order)
                ->where('id = ' . $unit->id);

        $db->setQuery($query);
        $db->execute();
      }

      $db->transactionCommit();
    }
    catch (Exception $e) {
      $db->transactionRollback();
    }
  }

  private function _clearSearchOrdering()
  {

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->update('#__unit')
            ->set('search_ordering = null')
            ->where('id <> 0');

    $db->setQuery($query);

    try {
      $return = $db->execute();
    }
    catch (Exception $e) {
      throw new Exception($e->getMessage());
    }

    return $return;
  }

  /*
   * Get a list of properties due to expire and are set to manual renewal
   */

  private function _getProps($auto = false)
  {

    //$this->out('Getting props...');

    $db = JFactory::getDBO();
    /**
     * Get the date
     */
    $date = JFactory::getDate();

    $query = $db->getQuery(true);

    $query->select('
      b.id'
    );

    // Select from the property table
    $query->from('#__property a');

    // Join the unit table
    $query->leftJoin('#__unit b ON b.property_id = a.id');

    // Live properties, that are published
    $query->where('expiry_date >= ' . $db->quote($date->calendar('Y-m-d')));
    $query->where('a.published = 1');
    $query->where('b.published = 1');
    $query->order('rand()');

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

JApplicationCli::getInstance('RandomSearchCron')->execute();
