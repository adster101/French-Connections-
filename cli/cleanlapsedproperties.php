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


/**
 * Cron job to trash expired cache data.
 *
 * @since  2.5
 */
class CleanLapsedProperties extends JApplicationCli
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
    $props = $this->_getProps();
	}

  /*
   * Get a list of properties that have expired
   *
   */

  private function _getProps()
  {

    //$this->out('Getting props...');

    $db = JFactory::getDBO();
    /**
     * Get the date
     */
    $date = JFactory::getDate('-6 years');

    $query = $db->getQuery(true);

    $query->select('
      a.id PRN, b.id as UNITID, a.expiry_date'
    );

    // Select from the property table
    $query->from('#__property a');

    // Join the unit table
    $query->leftJoin('#__unit b ON b.property_id = a.id');

    // Live properties, that are published
    $query->where('expiry_date <= ' . $db->quote($date->calendar('Y-m-d')));
    $query->where('a.published = 1');
    $query->where('b.published = 1');

    $db->setQuery($query);
    echo $query->__toString();
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

JApplicationCli::getInstance('CleanLapsedProperties')->execute();
