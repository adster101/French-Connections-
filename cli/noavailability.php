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

require_once JPATH_BASE . '/administrator/components/com_fcadmin/models/noavailability.php';
require_once JPATH_BASE . '/administrator/components/com_notes/models/note.php';
jimport('clickatell.SendSMS');

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class NoAvailabilityCron extends JApplicationCli
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
    $app = JFactory::getApplication('site');
    $lang = JFactory::getLanguage();
    $sms_params = JComponentHelper::getParams('com_rental');

    // Load the FcAdmin language file
    $lang->load('com_fcadmin', JPATH_ADMINISTRATOR, null, false, true);

    // Get the debug setting
    $debug = (bool) $app->getCfg('debug');
    define('JDEBUG', $debug);

    // Get an instance of the Noavailability FcAdminModel
    $model = JModelLegacy::getInstance('Noavailability', 'FcAdminModel', $config = array('ignore_request' => true));

    // Get an instance of the note model so we can save the email to the notes
    $note = JModelLegacy::getInstance('Note', 'NotesModel', $config = array('table_path' => JPATH_BASE . '/administrator/components/com_notes/tables/'));

    // A list of properties which need reminding about their availability
    $items = $model->getItems();

    try
    {

      foreach ($items as $item)
      {
        $body = JText::sprintf('COM_FCADMIN_NO_AVAILABILITY_CRON_EMAIL', $item->firstname);
        $subject = JText::sprintf('COM_FCADMIN_NO_AVAILABILITY_CRON_EMAIL_SUBJECT', $item->unit_title);
        $email = JDEBUG ? 'adamrifat@frenchconnections.co.uk' : $item->email;
        $from = $app->getCfg('mailfrom');
        $sender = $app->getCfg('fromname');

        // Send the email
        if (JFactory::getMailer()->sendMail($from, $sender, $email, $subject, $body) !== false)
        {
          // Log the email into the notes table...
          $data = array('id' => '', 'property_id' => $item->PRN, 'subject' => $subject, 'body' => $body);

          if (!$note->save($data))
          {
            // Throw an exception...cry...boo hoo!
          }

          // Only fire up the SMS bit if the owner is subscribed to SMS alerts...
          if ($item->sms_valid)
          {

            $sms = new SendSMS($sms_params->get('username'), $sms_params->get('password'), $sms_params->get('id'));
            // If the login return 0, means that login failed, you cant send sms after this 
            if (!$sms->login())
            {
              continue;
            }

            // Send sms using the simple send() call 
            if (!$sms->send($item->sms_alert_number, JText::sprintf('COM_FCADMIN_NO_AVAILABILITY_CRON_SMS', $item->firstname, $item->unit_title)))
            {
              continue;
            }
          }
        }
      }
    }
    catch (Exception $e)
    {
      print_r($e);
    }
  }

}

JApplicationCli::getInstance('NoAvailabilityCron')->execute();
