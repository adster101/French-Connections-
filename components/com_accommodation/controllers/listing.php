<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 */
class AccommodationControllerListing extends JControllerForm {

  public function renewals() {

    // Include all the model and helper files we need to process 
    require_once JPATH_BASE . '/libraries/frenchconnections/models/payment.php';
    require_once JPATH_ADMINISTRATOR . '/components/com_helloworld/models/listing.php';
    JLoader::register('HelloWorldHelper', JPATH_ADMINISTRATOR . '/components/com_helloworld/helpers/helloworld.php');
    $payment_summary_layout = new JLayoutFile('payment_summary', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');


    // Create an instance of the site application 
    $app = JFactory::getApplication('site');

    $debug = $app->getCfg('debug');

    // Get a list of properties for renewals
    $props = $this->_getProps();

    // Get the parameters for use in processing the renewal reminders
    $params = JComponentHelper::getParams('com_helloworld'); // These are the email params. 
    $renewal_templates = JComponentHelper::getParams('com_autorenewals'); // These are the renewal reminder email templates

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
      $email = true;

      $recipient = ($debug) ? 'adamrifat@frenchconnections.co.uk' : 'adamrifat@frenchconnections.co.uk';
      $cc = ($debug) ? '' : 'accounts@frenchconnections.co.uk';

      SWITCH (true) {
        case ($v->days == "30"):

          $body = JText::sprintf(
                          $renewal_templates->get('AUTO_RENEWAL_30_DAYS'), $user->firstname, $expiry_date, $payment_summary_layout->render($payment_summary), $total, $v->id
          );
          $subject = JText::sprintf($renewal_templates->get('AUTO_RENEWAL_30_DAYS_SUBJECT'), $v->id);
          break;

        case ($v->days == "7"):

          // Take shadow payment... 
          if (!$payment_model->processRepeatPayment($v->VendorTxCode, $v->VPSTxId, $v->SecurityKey, $v->TxAuthNo, 'REPEATDEFERRED', $payment_summary)) {

            // Problemo - shadow payment failed so generate email
            $body = JText::sprintf(
                            $renewal_templates->get('AUTO_RENEWAL_7_DAYS'), $user->firstname, $expiry_date, $payment_summary_layout->render($payment_summary), $total
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
            
          } else {
            // Problemo
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
      }

      // Send the email
      if ($email) {
        $payment_model->sendEmail('noreply@frenchconnections.co.uk', $recipient, '[TESTING] - ' . $subject, $body, $cc);
      }
    }
  }

  /*
   * Get a list of properties due for renewal
   */

  private function _getProps($auto = true) {
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
    $query->select('a.id, datediff(a.expiry_date, now()) as days, a.expiry_date, b.id as TxID, b.VendorTxCode, VPSTxId, SecurityKey, TxAuthNo');
    $query->from('#__property a');
    $query->where('expiry_date >= ' . $db->quote($date->calendar('Y-m-d')));
    $query->where('datediff(expiry_date, now()) in (-1,0,1,7,14,21,30)');
    if (!$auto) {
      $query->where('VendorTxCode = \'\'');
    } else {
      $query->join('left', '#__protx_transactions b on b.id = a.VendorTxCode');
      $query->where('a.VendorTxCode > 0');
    }

    $db->setQuery($query);

    try {
      $rows = $db->loadObjectList();
      //var_dump($rows);die;
    } catch (Exception $e) {
      return false;
    }

    return $rows;
  }

  public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true)) {
    return parent::getModel($name, $prefix, array('ignore_request' => false));
  }

  public function viewsite() {
    // Check for request forgeries.
    JSession::checkToken('GET') or jexit(JText::_('JINVALID_TOKEN'));

    $stub = $this->input->get('id', '', 'int');
    $ip = $_SERVER['REMOTE_ADDR'];
    $id = (int) $stub;

    // Prepare a db query so we can get the website address
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('a.website')
            ->from('#__property_versions a')
            ->where('a.property_id = ' . $id)
            ->where('a.website !=\'\'');

    $db->setQuery($query);

    try {

      $result = $db->loadRow();

      if (parse_url($result[0])) { // We have a valid web address 
        $website = $result[0];

        // Check that the http:// bit is present, if not add it. Should validate urls better 
        $website = (strpos($website, 'http://') === 0) ? $website : 'http://' . $website;

        // Log the view
        $query->getQuery(true);

        $columns = array('property_id', 'date_created', 'url', 'ip');

        $query->insert('#__website_views');
        $query->columns($columns);

        // Get the date
        $date = JFactory::getDate()->toSql();

        $data = array($db->quote($id), $db->quote($date), $db->quote($website), $db->quote($ip));

        // Update the value in the db        
        $query->values(implode(',', $data));

        $db->setQuery($query);

        $db->execute();

        // Redirect the user to the actual flippin' website
        $this->setRedirect(JRoute::_($website, false));
      }
    } catch (Exception $e) {
      // Log error   
      throw new Exception(JText::sprintf('COM_ACCOMMODATION_ERROR_FETCHING_WEBSITE_DETAILS_FOR', $id, $e->getMessage()), 500);
    }
  }

  public function enquiry() {
    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $app = JFactory::getApplication();
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/models');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables');
    $model = $this->getModel();
    $params = JComponentHelper::getParams('com_enquiries');
    $id = $this->input->get('id', '', 'int');
    $unit_id = $this->input->get('unit_id', '', 'int');

    // Get the data from POST
    $data = $this->input->post->get('jform', array(), 'array');

    // Get the property details we are adding an enquiry for.
    // Check for a valid session cookie
    if ($params->get('validate_session', 0)) {
      if (JFactory::getSession()->getState() != 'active') {
        JError::raiseWarning(403, JText::_('COM_CONTACT_SESSION_INVALID'));

        // Save the data in the session.
        $app->setUserState('com_accommodation.enquiry.data', $data);

        // Redirect back to the contact form.
        $this->setRedirect(JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $id . '&unit_id=' . (int) $unit_id . '#email', false));
        return false;
      }
    }

    // Validate the posted data.
    $form = $model->getForm();
    if (!$form) {
      JError::raiseError(500, $model->getError());
      return false;
    }

    // Validate the data. 
    // Returns either false or the validated, filtered data.
    $validate = $model->validate($form, $data);

    // TO DO - Possibly better to move save from model to here?


    if ($validate === false) {
      // Get the validation messages.
      $errors = $model->getErrors();
      // Push up to five validation messages out to the user.
      for ($i = 0, $n = count($errors); $i < $n && $i < 5; $i++) {
        if ($errors[$i] instanceof Exception) {
          $app->enqueueMessage($errors[$i]->getMessage(), 'error');
        } else {
          $app->enqueueMessage($errors[$i], 'error');
        }
      }

      // Trap any errors 
      $errors = $app->getMessageQueue();

      // Save the data in the session.
      $app->setUserState('com_accommodation.enquiry.data', $data);
      $app->setUserState('com_accommodation.enquiry.messages', $errors);

      // Redirect back to the contact form.
      $this->setRedirect(JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $id . '&unit_id=' . (int) $unit_id . '#email', false));
      return false;
    }


    // Write the review into the reviews table...
    if (!$model->processEnquiry($validate, $params, $id, $unit_id)) {

      // Set the message
      $msg = JText::_('COM_ENQUIRY_PROBLEM_SENDING_ENQUIRY');

      // Save the data in the session.
      $app->setUserState('com_accommodation.enquiry.data', $data);

      // Redirect back to the contact form.
      $this->setRedirect(JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $id . '&unit_id=' . (int) $unit_id . '#email', false), $msg);

      return false;
    }


    // Flush the data from the session
    // $app->setUserState('com_accommodation.enquiry.data', null);
    // Redirect if it is set in the parameters, otherwise redirect back to where we came from
    if ($params->get('redirect')) {
      $this->setRedirect(JRoute::_('index.php?option=com_content&Itemid=' . (int) $params->get('redirect')));
    } else {
      $this->setRedirect(JRoute::_('/'));
    }

    return true;
  }

}
