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
class CliTest extends JApplicationCli {

  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */
  public function doExecute() {

     $props = $this->_getProps();

    require_once JPATH_BASE . '/libraries/frenchconnections/models/payment.php';
    require_once JPATH_ADMINISTRATOR . '/components/com_helloworld/models/listing.php';

    $params = JComponentHelper::getParams('com_helloworld');

    foreach ($props as $k => $v) {
      $listing_model = JModelLegacy::getInstance('Listing', 'HelloWorldModel', $config = array('ignore_request' => true));

      $listing_model->setState('com_helloworld.listing.id', $v->id);

      $listing = $listing_model->getItems();

      $payment = JModelLegacy::getInstance('Payment', 'FrenchConnectionsModel', $config = array('listing' => $listing));
      $details = $payment->getPaymentSummary();
      
      $payment->sendEmail('noreply@frenchconnections.co.uk', 'adamrifat@frenchconnections.co.uk', 'Test renewal reminder for ' . $v->id, 'Test renewal reminder', $params);

    }
    
    $this->out('We done...');
    
  }

  /*
   * Get a list of properties due for renewal
   */

  private function _getProps() {

    //$this->out('Getting props...');

    $db = JFactory::getDBO();

    $query = $db->getQuery(true);
    $query->select('id, datediff(expiry_date, now()) as days');
    $query->from('#__property');
    $query->where('expiry_date > ' . $db->quote(JFactory::getDate()->calendar('Y-m-d')));
    $query->where('datediff(expiry_date, now()) in (1,7,14,21,30)');
    $query->where('VendorTxCode = \'\'');

    $db->setQuery($query);

    try {
      $rows = $db->loadObjectList();
    } catch (Exception $e) {
      var_dump($e);
      return false;
    }

    return $rows;
  }

}

JApplicationCli::getInstance('GarbageCron')->execute();
