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

// Require the additional model needed for processing. 
require_once JPATH_LIBRARIES . '/frenchconnections/models/payment.php';
require_once JPATH_ADMINISTRATOR . '/components/com_helloworld/models/listing.php';

// Register the Helloworld helper method
JLoader::register('HelloWorldHelper', JPATH_ADMINISTRATOR . '/components/com_helloworld/helpers/helloworld.php');

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class Renewals extends JApplicationCli {

  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */
  public function doExecute() {

    // Create an instance of the site application - needed for the CLI app to run the JLayout
    $app = JFactory::getApplication('site');

    // Get the debug setting
    $debug = (bool) $app->getCfg('debug');

    // This layout is used for the payment summary bit on pro forma invoices and renewal reminders/invoices etc
    $payment_summary_layout = new JLayoutFile('payment_summary', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');

    // Get the renewal template emails 
    $renewal_templates = JComponentHelper::getParams('com_autorenewals'); // These are the renewal reminder email templates
    // Process the auto renewals
    // $autorenewals = $this->_autorenewals($debug, $payment_summary_layout, $renewal_templates);
    // Process the manual renewals
    $manualrenewals = $this->_manualrenewals($debug, $payment_summary_layout, $renewal_templates);
  }

  private function _manualrenewals($debug = false, JLayoutFile $payment_summary_layout, JRegistry $renewal_templates) {

    $props = $this->_getProps();

    if (!$props) {
      $this->out('We done...');
      die;
    }

    // Process the renewal reminders
    foreach ($props as $k => $v) {

      $expiry_date = JFactory::getDate($v->expiry_date)->calendar('d M Y');

      // Get an instance of the listing model
      $listing_model = JModelLegacy::getInstance('Listing', 'HelloWorldModel', $config = array('ignore_request' => true));

      // Set the listing ID we are sending the reminder to 
      $listing_model->setState('com_helloworld.listing.id', $v->id);

      // Get a breakdown of the listing - returns an array of units.
      $listing = $listing_model->getItems();

      // Get an instance of the payment model
      $payment_model = JModelLegacy::getInstance('Payment', 'FrenchConnectionsModel', $config = array('listing' => $listing));

      $user = $payment_model->getUser($listing[0]->created_by);
      $payment_summary = $payment_model->getPaymentSummary();
      $total = $payment_model->getOrderTotal($payment_summary);

      $recipient = ($debug) ? 'accounts@frenchconnections.co.uk' : 'adamrifat@frenchconnections.co.uk';

      SWITCH (true) {
        case ($v->days < 0):
          $body = JText::sprintf(
                          $renewal_templates->get('RENEWAL_REMINDER_EXPIRED'), $user->firstname
          );
          $subject = JText::sprintf($renewal_templates->get('RENEWAL_REMINDER_SUBJECT_EXPIRED'), $v->id);
          break;
        case ($v->days == "1"):
          $body = JText::sprintf(
                          $renewal_templates->get('RENEWAL_REMINDER_DAYS_1'), $user->firstname, $v->id, $expiry_date, $payment_summary_layout->render($payment_summary), $total, $expiry_date
          );
          $subject = JText::sprintf($renewal_templates->get('RENEWAL_REMINDER_SUBJECT_1_DAYS'), $v->id);
          break;
        case ($v->days == "7"):
          $body = JText::sprintf(
                          $renewal_templates->get('RENEWAL_REMINDER_DAYS_7'), $user->firstname, $expiry_date, $payment_summary_layout->render($payment_summary), $total
          );
          $subject = JText::sprintf($renewal_templates->get('RENEWAL_REMINDER_SUBJECT_7_DAYS'), $v->id);
          break;
        case ($v->days == "14"):
          $body = JText::sprintf(
                          $renewal_templates->get('RENEWAL_REMINDER_DAYS_14'), $user->firstname, $expiry_date, $payment_summary_layout->render($payment_summary), $total, $v->id
          );
          $subject = JText::sprintf($renewal_templates->get('RENEWAL_REMINDER_SUBJECT_14_DAYS'), $v->id);
          break;
        case ($v->days == "21"):
          $body = JText::sprintf(
                          $renewal_templates->get('RENEWAL_REMINDER_DAYS_21'), $user->firstname, $expiry_date, $payment_summary_layout->render($payment_summary), $total, $v->id
          );
          $subject = JText::sprintf($renewal_templates->get('RENEWAL_REMINDER_SUBJECT_21_DAYS'), $v->id);
          break;
        case ($v->days == "30"):
          $body = JText::sprintf(
                          $renewal_templates->get('RENEWAL_REMINDER_DAYS_30'), $user->firstname, $expiry_date, $payment_summary_layout->render($payment_summary), $total, $v->id
          );
          $subject = JText::sprintf($renewal_templates->get('RENEWAL_REMINDER_SUBJECT_30_DAYS'), $v->id);
          break;
      }

      $payment_model->sendEmail('noreply@frenchconnections.co.uk', $recipient, '[TESTING] - ' . $subject, $body);
    }

    $this->out('We done...');
  }

  private function _autorenewals() {
    // This layout is used for the payment summary bit on pro forma invoices and renewal reminders/invoices etc
    $payment_summary_layout = new JLayoutFile('payment_summary', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');

    // Get a list of properties for renewals
    $props = $this->_getProps(true);

    if (!$props) {
      $this->out('We done...');
      die;
    }

    // Get the parameters for use in processing the renewal reminders
    $params = JComponentHelper::getParams('com_helloworld'); // These are the email params. 
    // Get the renewal template emails 
    $renewal_template = JComponentHelper::getParams('com_autorenewals'); // These are the renewal reminder email templates
    // Put the below into a separate method?
    foreach ($props as $k => $v) {

      $expiry_date = JFactory::getDate($v->expiry_date)->calendar('d M Y');

      // Get an instance of the listing model
      $listing_model = JModelLegacy::getInstance('Listing', 'HelloWorldModel', $config = array('ignore_request' => true));

      // Set the listing ID we are sending the reminder to 
      $listing_model->setState('com_helloworld.listing.id', $v->id);

      // Get a breakdown of the listing - returns an array of units.
      $listing = $listing_model->getItems();

      // Get an instance of the payment model
      $payment_model = JModelLegacy::getInstance('Payment', 'FrenchConnectionsModel', $config = array('listing' => $listing));

      $user = $payment_model->getUser($listing[0]->created_by);
      $payment_summary = $payment_model->getPaymentSummary();
      $total = $payment_model->getOrderTotal($payment_summary);

      $recipient = ($debug) ? 'accounts@frenchconnections.co.uk' : 'adamrifat@frenchconnections.co.uk';

      SWITCH (true) {
        case ($v->days < 0):
          $body = JText::sprintf(
                          $renewal_template->get('RENEWAL_REMINDER_EXPIRED'), $user->firstname
          );
          $subject = JText::sprintf($renewal_template->get('RENEWAL_REMINDER_SUBJECT_EXPIRED'), $v->id);

          break;

        case ($v->days = "1"):


          break;
        case ($v->days = "7"):
          // Take shadow payment etc
          $body = JText::sprintf(
                          $renewal_template->get('RENEWAL_REMINDER_DAYS_7'), $user->firstname, $expiry_date, $payment_summary_layout->render($payment_summary), $total
          );
          $subject = JText::sprintf($renewal_template->get('RENEWAL_REMINDER_SUBJECT_7_DAYS'), $v->id);
          break;

        case ($v->days = "30"):
          $body = JText::sprintf(
                          $renewal_template->get('RENEWAL_REMINDER_DAYS_30'), $user->firstname, $expiry_date, $payment_summary_layout->render($payment_summary), $total, $v->id
          );
          $subject = JText::sprintf($renewal_template->get('RENEWAL_REMINDER_SUBJECT_30_DAYS'), $v->id);
          break;
      }

      $payment_model->sendEmail('noreply@frenchconnections.co.uk', $recipient, '[TESTING] - ' . $subject, $body, $params);
    }

    $this->out('We done...');
  }

  /*
   * Get a list of properties due to expire and are set to manual renewal
   */

  private function _getProps($auto = false) {

    //$this->out('Getting props...');

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
    $query->select('id, datediff(expiry_date, now()) as days, expiry_date, VendorTxCode');
    $query->from('#__property');
    $query->where('expiry_date >= ' . $db->quote($date->calendar('Y-m-d')));
    $query->where('datediff(expiry_date, now()) in (-1,1,7,14,21,30)');
    if (!$auto) {
      $query->where('VendorTxCode = \'\'');
    } else {
      $query->where('VendorTxCode > 0');
    }

    $db->setQuery($query);

    try {
      $rows = $db->loadObjectList();
    } catch (Exception $e) {
      return false;
    }

    return $rows;
  }

}

JApplicationCli::getInstance('Renewals')->execute();
