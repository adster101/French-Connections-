<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class EnquiriesControllerEnquiry extends JControllerForm {

  /**
   * Method override to check if you can edit an existing record.
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   * @since	1.6
   */
  protected function allowEdit($data = array(), $key = 'id') {

    $recordId = (int) isset($data[$key]) ? $data[$key] : 0;

    $user = JFactory::getUser();
    $userId = $user->get('id');

    // Check specific edit permission then general edit permission.
    if (JFactory::getUser()->authorise('core.edit', 'com_enquiries')) {
      return true;
    }

    // Check specific edit permission then general edit permission.
    if (JFactory::getUser()->authorise('core.edit.own', 'com_enquiries')) {

      // They have permission to edit own, but do they own?
      $ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;

      if (empty($ownerId) && $recordId) {
        // Need to do a lookup from the model.
        $record = $this->getModel()->getItem($recordId);
        if (empty($record)) {
          return false;
        }

        $ownerId = $record->owner_id;
      }

      // If the owner matches 'me' then do the test.
      if ($ownerId == $userId) {
        return true;
      }

      return false;
    }
    return false;
  }

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
    // Get the app/input details instance
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
