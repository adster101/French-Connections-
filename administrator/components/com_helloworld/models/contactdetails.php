<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modeladmin library
// If we implement populateState and getItem then we extend directly from JModelForm
jimport('joomla.application.component.modeladmin');


jimport('clickatell.SendSMS');

class HelloWorldModelContactDetails extends JModelAdmin {

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_helloworld.edit.contactdetails.data', array());

    if (empty($data)) {
      $data = $this->getItem();
    }

    return $data;
  }

  /**
   * 
   */
  public function getItem($pk = null) {

    $data = parent::getItem($pk);

    $id = ($data->property_id) ? $data->property_id : '';

    $model = JModelLegacy::getInstance('Property', 'HelloWorldModel');
    $sms = $model->getSMSDetails($id);
    
    /*
     * See if there are any SMS prefs saved against this property
     */



    return $data;
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
    $form = $this->loadForm('com_helloworld.contactdetails', 'contactdetails', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }

    return $form;
  }

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'PropertyVersions', $prefix = 'HelloWorldTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * 
   * 
   */
  public function preprocessForm(\JForm $form, $data, $group = 'content') {

    if (!empty($data)) {

      // This is the case where data is already being passed into the form?
      $form->setFieldAttribute('use_invoice_details', 'default', '0');
    } else {

      $form_data = JFactory::getApplication()->input->get('jform', array(), 'array');

      if ($form_data['use_invoice_details']) {

        $form->setFieldAttribute('first_name', 'required', 'false');
        $form->setFieldAttribute('surname', 'required', 'false');
        $form->setFieldAttribute('phone_1', 'required', 'false');
        $form->setFieldAttribute('phone_2', 'default', '');
        $form->setFieldAttribute('phone_3', 'default', '');
        $form->setFieldAttribute('fax', 'default', '');
        $form->setFieldAttribute('email_1', 'required', 'false');
        $form->setFieldAttribute('email_2', 'default', '');
      }
    }
  }

  /**
   * Overriden save method. Use the propertyversions model to save the data...
   *  
   */
  public function save($data) {
    
    $model = JModelLegacy::getInstance('PropertyVersions', 'HelloWorldModel');
    
    /*
     * Get the SMS related values from the form
     */
    $valid = ($data['sms_valid']) ? $data['sms_valid'] : '';
    $sms_number = (isset($data['sms_alert_number'])) ? $data['sms_alert_number'] : ''; 
    $sms_verification_code = (isset($data['dummy_validation_code'])) ? $data['dummy_validation_code'] : ''; 
    
    /*
     * 
     */
    if ($sms_number && !$valid && !$sms_verification_code) {
      
      $code = rand(6,8);
      $data['sms_validation_code'] = $code;
      
    }
    
    if (!$model->save($data)) {
      // TO DO - Need to go trhough the property versions save model and throw exceptions rather than returing false.
      $error = $model->getError();

      $this->setError($error);
      return false;
    }



    /*
     * If we have an sms number but it's not been validated, send a verification code.
     * 
     */
    if ($sms_number && !$valid) {

      // Clickatel baby
      $sendsms = new SendSMS('frenchconnections', 'aefeadmgjs949', 3436516);

      /* if the login return 0, means that login failed, you cant send sms after this */
      if (($sendsms->login()) == 0) {
        die("failed");
      }

      /* other wise, you can send sms using the simple send() call */
      $sendsms->send("447799434206", "can you receive this message? Hello there....");
    }

    return true;
  }

}

