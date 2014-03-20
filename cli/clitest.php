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

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class GarbageCron extends JApplicationCli {

  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */
  public function doExecute() {

    $props = $this->_getProps();
    
    foreach($props as $prop) {
      $this->out($prop);
    }
    
  }

  /*
   * Get a list of properties due for renewal
   */

  private function _getProps() {

    $this->out('Getting props...');

    $db = JFactory::getDBO();

    $query = $db->getQuery(true);
    $query->select('id');
    $query->from('#__property');
    $query->where('expiry_date > ' . $db->quote(JFactory::getDate()->calendar('Y-m-D')));
    $query->where('datediff(expiry_date, now() in (1,7,14,21,30');
    $query->where('VendorTxId = \'\'');

    $db->setQuery($query);

    try {
      $rows = $db->loadObjectList();
      
    } catch (Exception $e) {
      var_dunp($e);
      return false;
    }
    
    return $rows;
    
  }

}

JApplicationCli::getInstance('GarbageCron')->execute();
