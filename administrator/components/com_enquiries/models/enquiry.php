<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class EnquiriesModelEnquiry extends JModelAdmin {

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Enquiry', $prefix = 'EnquiriesTable', $config = array()) {

    return JTable::getInstance($type, $prefix, $config);
  }

  /*
   * Override getItem so we can set the date format
   */
  public function getItem($pk = null) {
    if ($item = parent::getItem($pk)) {
      
      $item->date_created = JFactory::getDate($item->date_created)->calendar('m D Y');
      $item->start_date = ($item->start_date != '0000-00-00') ? JFactory::getDate($item->start_date)->calendar('m D Y') : 'N/A';
      $item->end_date = ($item->end_date != '0000-00-00') ? JFactory::getDate($item->end_date)->calendar('m D Y') : 'N/A';
      
    }
    
    return $item;
  }
  
  /**
   * Method to get the record form.
   *
   * @param	array	$data		Data for the form.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	mixed	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = true) {

    // Get the form.
    $form = $this->loadForm('com_enquiries.enquiries', 'enquiry', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }
    return $form;
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_enquiries.edit.enquiry.data', array());

    if (empty($data)) {
      $data = $this->getItem();
    }

    return $data;
  }

  /*
   * Method to preprocess the special offer edit form
   *
   * params
   * $form
   * $data
   *
   */

  protected function preprocessForm(JForm $form, $data) {


    $subject = JText::_('COM_ENQUIRIES_ENQUIRY_REPLY_SUBJECT');
    $message = JText::sprintf('COM_ENQUIRIES_ENQUIRY_REPLY_MESSAGE', ucfirst($data->forename));

    $form->setValue('reply_subject', null, $subject);

    $form->setValue('reply_message', null, $message);
  }

  public function sendReply($data = array()) {

    /*
     * Check that we have the details we need to proceed
     */
    if (empty($data['email']) || empty($data['reply_subject']) || empty($data['reply_message'])) {

      return false;
    }

    /*
     * Need to check whether the user has overriden the default contact details
     */
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables');

    $property = $this->getTable('PropertyVersions', 'HelloWorldTable');

    if (!$property->load($data['property_id'], false)) {
      return false;
    }

    if ($property->use_invoice_details) {

      $user = JFactory::getUser();

      /*
       * ATM the account detail doesn't hold a separate email address. 
       * This is consistent with the current system, although they are able to update their invoice
       * email address if they wish. Basically means that email address on the invoice screen
       * needs to be taken from the joomla user account field and update there also.
       * 
       * Account edit screen (non-Joomla account).
       * This needs to allow the user to update all aspects of their billing details (email, VAT status etc)
       * 
       * Account edit screen (Joomla)
       * Needs to allow the user to only update their password. 
       * Need to supress the email address from showing on the user edit profile screen. 
       * 
       * 
       * Or merge the account management into one screen including their login details etc...
       * 
       * Above also holds for the name. We will want to take firstname and surname from the sign up
       * and populate the basic invoice details. This will override the default Joomla account details.
       * 
       * Or surpress the account edit screen from the owner... 
       * 
       * $account_table = $this->getTable('UserProfileFc', 'HelloWorldTable');
       * if (!$account_table->load($user->id)) {
       *   return false;
       * }
       * 
       */

      $data['from_email'] = $user->email;
      $data['from_name'] = $user->name;
    } else {

      /*
       * Take the email details from the overriden contact details...
       */
      $data['from_email'] = $property->email_1;
      $data['from_name'] = $property->first_name . ' ' . $property->surname;
    }

    /*
     * Get the component params
     */
    $params = JComponentHelper::getParams('com_enquiries');

    // import our payment library class
    jimport('frenchconnections.models.payment');

    $model = JModelLegacy::getInstance('Payment', 'FrenchConnectionsModel', array('ignore_request' => true));

    $from = ($data['from_email']) ? $data['from_email'] : '';
    $to = ($data['email']) ? $data['email'] : '';
    $subject = $data['reply_subject'];
    $body = $data['reply_message'];

    if (!$model->sendEmail($from, $to, $subject, $body, $params, 'admin_enquiry_email')) {
      return false;
    }

    /*
     * It's all gravy - do we want to add a reply sent on field to the database?
     */ 
     $reply = array();
     $reply['id'] = $data['id'];
     $reply['replied'] = 1;
     $reply['date_replied'] = JFactory::getDate()->toSql();
     $this->save($reply);
    
    return true;

  }
}