<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
/**
 * HelloWorld Model
 */
class EnquiriesModelEnquiry extends JModelAdmin
{

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Enquiry', $prefix = 'EnquiriesTable', $config = array())
  {

    return JTable::getInstance($type, $prefix, $config);
  }

  /*
   * Override getItem so we can set the date format
   */

  public function getItem($pk = null)
  {

    if ($item = parent::getItem($pk))
    {

      $item->date_created = JFactory::getDate($item->date_created)->calendar('d M Y');
      $item->start_date = ($item->start_date != '0000-00-00') ? JFactory::getDate($item->start_date)->calendar('d M Y') : 'N/A';
      $item->end_date = ($item->end_date != '0000-00-00') ? JFactory::getDate($item->end_date)->calendar('d M Y') : 'N/A';
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
  public function getForm($data = array(), $loadData = true)
  {

    // Get the form.
    $form = $this->loadForm('com_enquiries.enquiries', 'enquiry', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
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
  protected function loadFormData()
  {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_enquiries.edit.enquiry.data', array());

    if (empty($data))
    {
      $data = $this->getItem();
    }

    return $data;
  }

  public function markAsRead($id = '')
  {

    if (empty($id))
    {
      return true;
    }

    // Need to check the current status of this enquiry. If already read, just do nout.
    if ($enquiry = $this->getItem($id))
    {

      if ($enquiry->state == 0)
      {
        $enquiry->state = 1;

        $enquiry = $enquiry->getProperties();

        if ($this->save($enquiry))
        {
          return true;
        }
      }
    }


    return true;
  }

  /*
   * Method to preprocess the special offer edit form
   *
   * params
   * $form
   * $data
   *
   */

  protected function preprocessForm(JForm $form, $data)
  {


    $subject = JText::_('COM_ENQUIRIES_ENQUIRY_REPLY_SUBJECT');
    $message = JText::sprintf('COM_ENQUIRIES_ENQUIRY_REPLY_MESSAGE', ucfirst($data->guest_forename));

    $form->setValue('reply_subject', null, $subject);

    $form->setValue('reply_message', null, $message);
  }

  public function processFailedEnquiries($enqs = array(), $state = '0')
  {
    // Set up the bits and pieces 
    $user = JFactory::getUser();
    $params = JComponentHelper::getParams('com_enquiries');
    JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_accommodation/models');
    $lang = JFactory::getLanguage();
    $lang->load('com_accommodation', JPATH_SITE);
    
    if (!$user->authorise('core.admin', 'com_enquiries'))
    {
      return false;
    }

    foreach ($enqs as $enquiry)
    {
      $detail = $this->getItem($enquiry);

      if (!$detail)
      {
        return false;
      }
      
      $enquiry_detail = JArrayHelper::fromObject($detail);
      
      $model = JModelLegacy::getInstance('Listing','AccommodationModel');
      
      $model->getState();
      $model->setState('property.id', $enquiry_detail['property_id']);
      $model->setState('unit.id', $enquiry_detail['unit_id']);
      $model->processEnquiry($enquiry_detail, $params, $enquiry_detail['property_id'], $enquiry_detail['unit_id'], true);
      
      
      
    }

    return true;
  }

  public function sendReply($data = array())
  {

    $table = $this->getTable();

    // Bind the data
    if (!$table->bind($data))
    {
      $this->setError($table->getError());
      return false;
    }

    // Check for empty values
    if (empty($table->guest_email))
    {
      return false;
    }
    // Assign empty values
    if (empty($table->guest_email))
    {
      return false;
    }
    // Assign empty values
    if (empty($table->guest_email))
    {
      return false;
    }
    /*
     * Check that we have the details we need to proceed
     */
    if (empty($data['guest_email']) || empty($data['reply_subject']) || empty($data['reply_message']))
    {

      return false;
    }

    /*
     * Need to check whether the user has overriden the default contact details
     */
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');

    $property = $this->getTable('PropertyVersions', 'RentalTable');

    if (!$property->load($data['property_id'], false))
    {
      return false;
    }

    if ($property->use_invoice_details)
    {

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
       * $account_table = $this->getTable('UserProfileFc', 'RentalTable');
       * if (!$account_table->load($user->id)) {
       *   return false;
       * }
       * 
       */

      $data['from_email'] = $user->email;
      $data['from_name'] = $user->name;
    }
    else
    {

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
    // From details are taken from the owners user account 
    $from = ($data['from_email']) ? $data['from_email'] : '';
    $from_name = ($data['from_name']) ? $data['from_name'] : '';
    // To is the holiday maker who made the initial enquiry
    $to = ($data['email']) ? $data['email'] : '';

    $subject = $data['reply_subject'];
    $body = $data['reply_message'];

    $recipient = (JDEBUG) ? $params->get('admin_enquiry_email', 'adamrifat@frenchconnections.co.uk') : $to;

    // Assemble the email data...
    $mail = JFactory::getMailer()
            ->addRecipient($recipient)
            ->setSubject($subject)
            ->setBody($body);
    $mail->setFrom($from, $from_name);


    if (!$mail->Send())
    {
      return false;
    }

    // Add the bcc if the owners want a copy of the email.
    if (!empty($data['cc_message']))
    {
      // If the owner wants a copy then this is 'cced' to the owner in a separate email.
      $cc_email_from = $params->get('admin_enquiry_no_reply', 'adamrifat@frenchconnections.co.uk');
      $cc_recipient = (JDEBUG) ? $params->get('admin_enquiry_email', 'adamrifat@frenchconnections.co.uk') : $from;

      // Assemble the email data...
      $mail = JFactory::getMailer()
              ->setSender($cc_email_from)
              ->addRecipient($cc_recipient)
              ->setSubject($subject)
              ->setBody($body);

      if (!$mail->Send())
      {
        //Log this out to a log file, not major, owner won't get email is all...
      }
    }



    /*
     * It's all gravy 
     */
    $reply = array();
    $reply['id'] = $data['id'];
    $reply['replied'] = 1;
    $reply['date_replied'] = JFactory::getDate()->toSql();

    // Make this a bit more elegant
    $this->save($reply);

    return true;
  }

}