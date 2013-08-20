<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('frenchconnections.controllers.property.base');

/**
 * HelloWorld Controller
 */
class EnquiriesControllerEnquiry extends HelloWorldControllerBase {

  /*
   * Function to reply to an owner enquiry.
   * Updates a date field in the enquiries table to indicate the owner replied.
   *
   *
   */

  public function reply() {

    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    
    // TO DO - Get the property and user contact details here.
    // Need to determine whether they have overridden the contact details
    // to an alternative one.
    // Also, need to verify the user sending the reply is the owner. So below needs to go into the enquiry model
    
    $input = JFactory::getApplication()->input;

    // Get the posted data
    $data = $input->post->get('jform', array(), 'array');

    if (!empty($data['email']) && !empty($data['reply_subject']) && !empty($data['reply_message'])) {
      // Send the email baby...
      // Send the email
      $sent = false;

      $sent = $this->_sendEmail($data);
    }

    // Set the success message if it was a success
    if (!($sent instanceof Exception)) {

      $msg = JText::_('COM_ENQUIRIES_ENQUIRY_REPLY_SENT');
      // Redirect if it is set in the parameters, otherwise redirect back to where we came from

      $this->setRedirect(JRoute::_('index.php?option=com_enquiries', $msg));
      return true;
    } else {
      $msg = '';
      $this->setRedirect(JRoute::_('index.php?option=com_enquiries', $msg));

      return false;
    }
  }

  private function _sendEmail($data = array()) {

    // Get the user details
    // These may be passed in from above...
    $user = JFactory::getUser();

    $user_details = JFactory::getUser($user->id);
    // Details of who is sending the email
    $mailfrom = $user_details->email;
    $fromname = $user_details->name;

    // Prepare email body

    $mail = JFactory::getMailer();

    $mail->addRecipient($data['email'], $data['forename'] . ' ' . $data['surname']);
    $mail->addReplyTo(array($mailfrom, $fromname));
    $mail->setSender(array($mailfrom, $fromname));
    $mail->setSubject($data['reply_subject']);
    $mail->setBody($data['reply_message']);
    $sent = $mail->Send();

    return $sent;
  }

}
