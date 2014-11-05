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
require_once JPATH_ADMINISTRATOR . '/components/com_rental/models/listing.php';

// Register the Helloworld helper method
JLoader::register('RentalHelper', JPATH_ADMINISTRATOR . '/components/com_rental/helpers/rental.php');

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
    $payment_summary_layout = new JLayoutFile('payment_summary', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');

    // Get the renewal template emails 
    $renewal_templates = JComponentHelper::getParams('com_autorenewals'); // These are the renewal reminder email templates

    // Process the manual renewals
    //$manualrenewals = $this->_manualrenewals($debug, $payment_summary_layout, $renewal_templates);
    
    // Process the auto renewals
    $autorenewals = $this->_autorenewals($debug, $payment_summary_layout, $renewal_templates);
    
  }

  private function _manualrenewals($debug = false, JLayoutFile $payment_summary_layout, JRegistry $renewal_templates) {
    
    // Array for holding a list of the contact notes
    $notes = array();
    
    // Get props due for manual renewal
    $props = $this->_getProps();

    if (!$props) {
      die;
    }

    $this->out('About to process manual renewal reminders');

    // Process the renewal reminders
    foreach ($props as $k => $v) {

      $expiry_date = JFactory::getDate($v->expiry_date)->calendar('d M Y');

      // Get an instance of the listing model
      $listing_model = JModelLegacy::getInstance('Listing', 'RentalModel', $config = array('ignore_request' => true));

      // Set the listing ID we are sending the reminder to 
      $listing_model->setState('com_rental.listing.id', $v->id);
      $listing_model->setState('com_rental.listing.latest', true);

      // Get a breakdown of the listing - returns an array of units.
      $listing = $listing_model->getItems();

      // Get an instance of the payment model
      $payment_model = JModelLegacy::getInstance('Payment', 'FrenchConnectionsModel', $config = array('listing' => $listing, 'renewal' => true));

      $user = $payment_model->getUser($listing[0]->created_by);
      $payment_summary = $payment_model->getPaymentSummary($listing);
      $total = $payment_model->getOrderTotal($payment_summary);

      $recipient = ($debug) ? 'accounts@frenchconnections.co.uk' : 'adamrifat@frenchconnections.co.uk';
      $cc = ($debug) ? 'adamrifat@frenchconnections.co.uk' : 'accounts@frenchconnections.co.uk';
      $send_email = true;

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
        default:
          $send_email = false;
          break;
      }
      if ($send_email) {
        // Send the email
        if ($payment_model->sendEmail('accounts@frenchconnections.co.uk', $recipient, '[TESTING] - ' . $subject, $body, $cc)) {
          // If the email is sent then write out to the notes table
          $notes[$v->id] = array('id' => '', 'subject' => $subject, 'body' => $body, 'property_id' => $v->id);
        }
      }
    }

    // Empty the notes array into the database
    if (!empty($notes)) {
      $this->saveNotes($notes);
    }

    $this->out('Done processing manual reminders...');
  }

  private function _autorenewals($debug = false, JLayoutFile $payment_summary_layout, JRegistry $renewal_templates) {

    // Array for holding a list of the contact notes
    $notes = array();

    // Get a list of properties for renewals
    $props = $this->_getProps(true);

    if (!$props) {
      die;
    }

    $this->out('About to process auto-renewal reminders');

    foreach ($props as $k => $v) {

      $expiry_date = JFactory::getDate($v->expiry_date)->calendar('d M Y');

      // Get an instance of the listing model
      $listing_model = JModelLegacy::getInstance('Listing', 'RentalModel', $config = array('ignore_request' => true));

      // Set the listing ID we are sending the reminder to 
      $listing_model->setState('com_rental.listing.id', $v->id);
      $listing_model->setState('com_rental.listing.latest', true);

      // Get a breakdown of the listing - returns an array of units.
      $listing = $listing_model->getItems();

      // Get an instance of the payment model
      $payment_model = JModelLegacy::getInstance('Payment', 'FrenchConnectionsModel', $config = array('listing' => $listing, 'renewal' => true));

      $user = $payment_model->getUser($listing[0]->created_by);
      $payment_summary = $payment_model->getPaymentSummary($listing);
      $total = $payment_model->getOrderTotal($payment_summary);
      $email = true;

      $recipient = ($debug) ? 'accounts@frenchconnections.co.uk' : 'adamrifat@frenchconnections.co.uk';
      $cc = ($debug) ? 'adamrifat@frenchconnections.co.uk' : 'accounts@frenchconnections.co.uk';

      SWITCH (true) {
        case ($v->days == "30"):

          $body = JText::sprintf(
                          $renewal_templates->get('AUTO_RENEWAL_30_DAYS'), $user->firstname, $expiry_date, $payment_summary_layout->render($payment_summary), $total, $v->id
          );
          $subject = JText::sprintf($renewal_templates->get('AUTO_RENEWAL_30_DAYS_SUBJECT'), $v->id, $expiry_date);
          break;

        case ($v->days == "7"):

          // Take shadow payment... 
          // May need to pass more info here, e.g. billing name, address, user id, 
          if (!$payment_model->processRepeatPayment($v->VendorTxCode, $v->VPSTxId, $v->SecurityKey, $v->TxAuthNo, 'REPEATDEFERRED', $payment_summary)) {

            // Problemo - shadow payment failed so generate email
            $body = JText::sprintf(
                            $renewal_templates->get('AUTO_RENEWAL_7_DAYS'), $user->firstname, $payment_summary_layout->render($payment_summary)
            );
            $subject = JText::sprintf($renewal_templates->get('AUTO_RENEWAL_7_DAYS_SUBJECT'), $v->id);
          } else {
            // Don't send an email here if the shadow payment was successful.
            $email = false;
          }

          break;

        case ($v->days == "0"):
          // Take actual payment
          if (!$payment_model->processRepeatPayment($v->VendorTxCode, $v->VPSTxId, $v->SecurityKey, $v->TxAuthNo, 'REPEAT', $payment_summary)) {
            $email = false;
          } else {
            // Success
            // Update listing details here, mainly just update the expiry date for the PRN
            $body = JText::sprintf(
                            $renewal_templates->get('AUTO_RENEWAL_SUCCESS'), $user->firstname, $expiry_date, $payment_summary_layout->render($payment_summary), $total
            );
            $subject = JText::sprintf($renewal_templates->get('AUTO_RENEWAL_SUCCESS_SUBJECT'), $v->id);
          }

          break;

        case ($v->days < 0):
          $body = JText::sprintf(
                          $renewal_templates->get('RENEWAL_REMINDER_EXPIRED'), $user->firstname
          );
          $subject = JText::sprintf($renewal_templates->get('RENEWAL_REMINDER_SUBJECT_EXPIRED'), $v->id);
          break;
        default:
          $email = false;
          break;
      }

      // Send the email
      if ($email) {

        $payment_model->sendEmail('accounts@frenchconnections.co.uk', $recipient, '[TESTING] - ' . $subject, $body, $cc);

        $notes[$v->id] = array('id' => '', 'subject' => $subject, 'body' => $body, 'property_id' => $v->id);
      }
    }

    if (!empty($notes)) {
      $this->saveNotes($notes);
    }

    $this->out('Done processing auto renewal reminders and payments');
  }

  public function saveNotes($notes = array()) {

    // Add the tables to the include path
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_notes/tables');

    // Get an instance of the note table
    $table = JTable::getInstance('Note', 'NotesTable');

    foreach ($notes as $note) {
      if (!$table->bind($note)) {
        return false;
      }

      if (!$table->store()) {
        return false;
      }

      $table->reset();
    }

    return true;
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
    $query->select('
      a.id, 
      datediff(a.expiry_date, now()) as days, 
      a.expiry_date, 
      b.id as TxID, 
      b.VendorTxCode,
      b.VPSTxId, 
      b.SecurityKey,
      b.TxAuthNo, 
      b.user_id, 
      b.property_id'
    );

    $query->from('#__property a');
    $query->where('expiry_date >= ' . $db->quote($date->calendar('Y-m-d')));
    $query->where('datediff(expiry_date, now()) in (-1,0,1,7,14,21,30)');
    $query->join('left', '#__protx_transactions b on b.id = a.VendorTxCode');

    if (!$auto) {
      $query->where('a.VendorTxCode = \'\'');
    } else {
      $query->where('a.VendorTxCode > 0');
    }

    $db->setQuery($query);

    try {
      $rows = $db->loadObjectList();
    } catch (Exception $e) {
      $this->out('Problem getting props...');
      return false;
    }

    return $rows;
  }

}

JApplicationCli::getInstance('Renewals')->execute();
