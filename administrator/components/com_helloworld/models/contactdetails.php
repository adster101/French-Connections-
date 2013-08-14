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

    $sms_details = $this->getSMSDetails($id);


    /*
     * See if there are any SMS prefs saved against this property
     */
    if (!empty($sms_details)) {

      $data->sms_alert_number = $sms_details->sms_alert_number;
      $data->sms_valid = $sms_details->sms_valid;
      $data->sms_status = $sms_details->sms_status;
    }


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
   * Verify the SMS number is also handled here.
   * TO DO - Get the SMS details up front. Then add a check to see if mobile number is set.
   * If the mobile number is different then trigger the validation process again (resetting all)
   * If empry then clear all and reset.
   *  
   */
  public function save($data) {

    $params = JComponentHelper::getParams('com_helloworld');
    
    /*
     * Get the property versions model
     */
    $model = JModelLegacy::getInstance('PropertyVersions', 'HelloWorldModel');

    /*
     * Get the existing SMS details for this property 
     */
    $sms_details = $this->getSMSDetails($data['property_id']);
    
    /*
     * Get the SMS related values from the validated form data
     */
    $valid = $data['sms_valid'];
    $sms_number =  $data['sms_alert_number'];
    $sms_verification_code = $data['dummy_validation_code'];
    $sms_status = $data['sms_status'];

    /*
     * Login flag to indicate whether we logged into clickatell okay
     */
    $login = false;
    
    if ($sms_number && !$valid && !$sms_status) {

      $code = rand(10000, 100000);
      $data['sms_validation_code'] = $code;
      $data['sms_status'] = 'VALIDATE';

      // Clickatel baby
      $sendsms = new SendSMS($params->get('username'), $params->get('password'), $params->get('id'));

      /*
       *  if the login return 0, means that login failed, you cant send sms after this 
       */
      if (($sendsms->login())) {
        $login = true;
      }

      /* 
       * Send sms using the simple send() call 
       */
      if ($login) {
        $sendsms->send($sms_number, JText::sprintf('COM_HELLOWORLD_HELLOWORLD_SMS_VERIFICATION_CODE', $code));
      }
      
    } else if ($sms_number && !$valid && $sms_status == 'VALIDATE') {

      /*
       * Get the validation code from the data base and compare it to that passed in via the form
       */

      $data['sms_validation_code'] = $sms_details->sms_validation_code;
      $data['sms_valid'] = 0;

      if ($sms_verification_code == $sms_details->sms_validation_code) {
        $data['sms_status'] = 'OK';
        $data['sms_valid'] = 1;
      }
    }

    if (!$model->save($data)) {
      // TO DO - Need to go trhough the property versions save model and throw exceptions rather than returing false.
      $error = $model->getError();

      $this->setError($error);
      return false;
    }

    return true;
  }

  /**
   * Method to get SMS alert status relating to a property reference number
   * TO DO - Cache the result in model scope for multiple uses.
   * 
   * @param type $id
   * @return boolean
   */
  
  public function getSMSDetails($id = '') {

    if (empty($id)) {
      return false;
    }

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    // Initialise the query.
    $query->select('sms_alert_number, sms_valid, sms_status, sms_validation_code');
    $query->from('#__property' . ' as a');
    $query->where('a.id = ' . (int) $id);

    $db->setQuery($query);

    $row = $db->loadObject();

    // Check that we have a result.
    if (empty($row)) {
      return false;
    }




    return $row;
  }

}

