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
    $this->payment_summary = new JLayoutFile('payment_summary', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');

    // Get a list of properties for renewals
    $props = $this->_getProps();

    // Get the parameters for use in processing the renewal reminders
    $params = JComponentHelper::getParams('com_helloworld'); // These are the email params. 
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

      SWITCH ($v->days) {
        case 1:
          $body = JText::sprintf(
                          $renewal_template->get('RENEWAL_REMINDER_DAYS_1'), $user->firstname, $v->id, $expiry_date, $this->payment_summary->render($payment_summary), $total, $expiry_date
          );
          $subject = JText::sprintf($renewal_template->get('RENEWAL_REMINDER_SUBJECT_1_DAYS'), $v->id);

          break;
        case 7:
          $body = JText::sprintf(
                          $renewal_template->get('RENEWAL_REMINDER_DAYS_7'), $user->firstname, $expiry_date, $this->payment_summary->render($payment_summary), $total
          );
          $subject = JText::sprintf($renewal_template->get('RENEWAL_REMINDER_SUBJECT_7_DAYS'), $v->id);
          break;

        case 14:
          $body = JText::sprintf(
                          $renewal_template->get('RENEWAL_REMINDER_DAYS_14'), $user->firstname, $expiry_date, $v->id, $this->payment_summary->render($payment_summary), $total
          );
          $subject = JText::sprintf($renewal_template->get('RENEWAL_REMINDER_SUBJECT_14_DAYS'), $v->id);

          break;
        case 21:
          $body = JText::sprintf(
                          $renewal_template->get('RENEWAL_REMINDER_DAYS_21'), $user->firstname, $expiry_date, $v->id, $this->payment_summary->render($payment_summary), $total
          );
          $subject = JText::sprintf($renewal_template->get('RENEWAL_REMINDER_SUBJECT_21_DAYS'), $v->id);

          break;
        case 30:
          $body = JText::sprintf(
                          $renewal_template->get('RENEWAL_REMINDER_DAYS_30'), $user->firstname, $expiry_date, $v->id, $this->payment_summary->render($payment_summary), $total
          );
          $subject = JText::sprintf($renewal_template->get('RENEWAL_REMINDER_SUBJECT_30_DAYS'), $v->id);
          break;
      }
      
      $payment_model->sendEmail('noreply@frenchconnections.co.uk', 'adamrifat@frenchconnections.co.uk', '[TESTING]' . $subject, $body, $params);

    }
  }

  /*
   * Get a list of properties due for renewal
   */

  private function _getProps() {

    //$this->out('Getting props...');

    $db = JFactory::getDBO();

    $query = $db->getQuery(true);
    $query->select('id, datediff(expiry_date, now()) as days, expiry_date');
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
