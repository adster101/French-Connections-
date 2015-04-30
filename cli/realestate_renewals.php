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

// Require the additional model needed for processing. 
require_once JPATH_ADMINISTRATOR . '/components/com_realestate/models/listing.php';

// Register the Helloworld helper method
JLoader::register('RentalHelper', JPATH_ADMINISTRATOR . '/components/com_rental/helpers/rental.php');

// Register the global PropertyHelper class
JLoader::register('PropertyHelper', JPATH_LIBRARIES . '/frenchconnections/helpers/property.php');

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class RealestateRenewals extends JApplicationCli
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
    // Create an instance of the site application - needed for the CLI app to run the JLayout
    $app = JFactory::getApplication('site');

    // Get the debug setting
    $debug = (bool) $app->getCfg('debug');

    // Get the renewal template emails 
    $renewal_templates = JComponentHelper::getParams('com_autorenewals'); // These are the renewal reminder email templates
    // Process the manual renewals
    $this->_manualrenewals($debug, $renewal_templates);
  }

  private function _manualrenewals($debug = false, JRegistry $renewal_templates)
  {

    $app = JFactory::getApplication();

    // Array for holding a list of the contact notes
    $notes = array();

    // Get props due for manual renewal
    $props = $this->_getProps();

    if (!$props)
    {
      return false;
    }

    $this->out('About to process manual renewal reminders');

    // Process the renewal reminders
    foreach ($props as $k => $v)
    {

      $expiry_date = JFactory::getDate($v->expiry_date)->calendar('d M Y');

      // Get an instance of the listing model
      $listing_model = JModelLegacy::getInstance('Listing', 'RealEstateModel', $config = array('ignore_request' => true));

      // Set the listing ID we are sending the reminder to 
      $listing_model->setState('com_realestate.listing.id', $v->id);
      $listing_model->setState('com_realestate.listing.latest', true);

      // Get a breakdown of the listing - returns an array of units.
      $listing = $listing_model->getItems();

      $recipient = ($debug) ? $app->getCfg('mailfrom', '') : $listing[0]->email;

      $send_email = true;

      SWITCH (true) {
        case ($v->days < 0):
          $body = JText::sprintf($renewal_templates->get('RENEWAL_REMINDER_EXPIRED'), $listing[0]->account_name);
          $subject = JText::sprintf($renewal_templates->get('RENEWAL_REMINDER_SUBJECT_EXPIRED'), $v->id);
          break;
        case ($v->days == "30"):
          $subject = JText::sprintf($renewal_templates->get('RENEWAL_REALESTATE_REMINDER_SUBJECT_30_DAYS'), $listing[0]->account_name, $v->id);
          $body = JText::sprintf($renewal_templates->get('RENEWAL_REALESTATE_REMINDER_DAYS_30'), $listing[0]->account_name, $v->id, $listing[0]->title, $expiry_date, $expiry_date);
          break;
        default:
          $send_email = false;
          break;
      }

      if ($send_email)
      {
        // Assemble the email data...
        $mail = JFactory::getMailer()
                ->setSender('accounts@frenchconnections.co.uk')
                ->addBCC('adamrifat@frenchconnections.co.uk')
                ->addBCC('accounts@frenchconnections.co.uk')
                ->addRecipient($recipient)
                ->setSubject($subject)
                ->setBody($body)
                ->isHtml(true);

        // Send the email
        if ($mail->send())
        {
          // If the email is sent then write out to the notes table
          $notes[$v->id] = array('id' => '', 'subject' => $subject, 'body' => $body, 'property_id' => $v->id);
        }
      }
    }

    // Empty the notes array into the database
    if (!empty($notes))
    {
      $this->saveNotes($notes);
    }

    $this->out('Done processing manual reminders...');
  }

  public function saveNotes($notes = array())
  {

    // Add the tables to the include path
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_notes/tables');

    // Get an instance of the note table
    $table = JTable::getInstance('Note', 'NotesTable');

    foreach ($notes as $note)
    {
      if (!$table->bind($note))
      {
        return false;
      }

      if (!$table->store())
      {
        return false;
      }
    }

    return true;
  }

  /*
   * Get a list of properties due to expire and are set to manual renewal
   */

  private function _getProps()
  {
    $users_to_ignore = array();
    $users_to_ignore[] = JUser::getInstance('allezfrancais')->id;
    $users_to_ignore[] = JUser::getInstance('frueda@realestatelanguedoc.com')->id;

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
      a.expiry_date'
    );

    $query->from('#__realestate_property a');
    $query->where('expiry_date >= ' . $db->quote($date->calendar('Y-m-d')));
    $query->where('datediff(expiry_date, now()) in (-1,30)');
    $query->where('a.created_by not in (' . implode(',', $users_to_ignore) . ')');

    $db->setQuery($query);

    try
    {
      $rows = $db->loadObjectList();
    }
    catch (Exception $e)
    {
      $this->out('Problem getting props...');
      return false;
    }

    return $rows;
  }

}

JApplicationCli::getInstance('RealestateRenewals')->execute();
