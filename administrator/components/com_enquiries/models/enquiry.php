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
    $message = JText::sprintf('COM_ENQUIRIES_ENQUIRY_REPLY_MESSAGE',$data->forename);
    
    $form->setValue('reply_subject',null,$subject);
    
    $form->setValue('reply_message',null,$message);

  }

}