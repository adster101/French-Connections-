<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modeladmin library
// If we implement populateState and getItem then we extend directly from JModelForm
jimport('joomla.application.component.modeladmin');


jimport('clickatell.SendSMS');

class RentalModelContactDetails extends JModelAdmin {

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_rental.edit.contactdetails.data', array());

    if (empty($data)) {
      $data = $this->getItem();
    }

    return $data;
  }

  /**
   * 
   */
  public function getItem($pk = null) {

    if ($data = parent::getItem($pk)) {

      $id = ($data->property_id) ? $data->property_id : '';

      $sms_details = $this->getSMSDetails($id);


      /*
       * See if there are any SMS prefs saved against this property
       */
      if (!empty($sms_details)) {

        $data->sms_alert_number = $sms_details->sms_alert_number;
        $data->sms_valid = $sms_details->sms_valid;
        $data->sms_status = $sms_details->sms_status;
        $data->sms_nightwatchman = $sms_details->sms_nightwatchman;
      }

      if (!empty($data->languages_spoken)) {
        // Convert the urls field to an array.
        $registry = new JRegistry;
        $registry->loadString($data->languages_spoken);
        $data->languages_spoken = $registry->toArray();
      }
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
    $form = $this->loadForm('com_rental.contactdetails', 'contactdetails', array('control' => 'jform', 'load_data' => $loadData));
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
  public function getTable($type = 'PropertyVersions', $prefix = 'RentalTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * 
   * 
   */
  public function preprocessForm(\JForm $form, $data, $group = 'content') {

    $input = JFactory::getApplication()->input->get('jform', false, 'array');

    if (empty($input['use_invoice_details']) && ($input)) {
      // User has selected not to use the invoice address. Therefore these fields are required.
      $form->setFieldAttribute('first_name', 'required', 'true');
      $form->setFieldAttribute('surname', 'required', 'true');
      $form->setFieldAttribute('phone_1', 'required', 'true');
      $form->setFieldAttribute('email_1', 'required', 'true');
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

    $params = JComponentHelper::getParams('com_rental');

    /*
     * Get the property versions model
     */
    $model = JModelLegacy::getInstance('PropertyVersions', 'RentalModel');

    /*
     * Get the existing SMS details for this property 
     */
    $sms_details = $this->getSMSDetails($data['property_id']);

    /*
     * Get the SMS related values from the validated form data
     */
    $valid = $data['sms_valid'];
    $sms_number = $data['sms_alert_number'];
    $sms_verification_code = $data['dummy_validation_code'];
    $sms_status = $data['sms_status'];

    /*
     * Login flag to indicate whether we logged into clickatell okay
     */
    $login = false;

    /*
     * If we have an sms number but it's not been validated and there we haven't send a verification code
     * OR
     * The sms number that has been passed is different to the one on record.
     */
    if (($sms_number && !$valid && !$sms_status) || (!empty($sms_number) && strcmp($sms_number, $sms_details->sms_alert_number) != 0)) {

      $code = rand(10000, 100000);
      $data['sms_validation_code'] = $code;
      $data['sms_status'] = 'VALIDATION';
      $data['sms_valid'] = 0;
      $data['sms_alert_number'] = $sms_number;

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
        $sendsms->send($sms_number, JText::sprintf('COM_RENTAL_HELLOWORLD_SMS_VERIFICATION_CODE', $code));
      }
    } else if (($sms_number) && !$valid && $sms_status == 'VALIDATION') { // The number hasn't been validated but we might have a validation code to verify

      /*
       * Get the validation code from the data base and compare it to that passed in via the form
       */

      $data['sms_validation_code'] = $sms_details->sms_validation_code;
      $data['sms_valid'] = 0;

      if ($sms_verification_code == $sms_details->sms_validation_code) {
        $data['sms_status'] = 'ACTIVE';
        $data['sms_valid'] = 1;
      }
    } else if (empty($sms_number)) { // Opt out of alerts
      $data['sms_validation_code'] = '';
      $data['sms_status'] = '';
      $data['sms_valid'] = 0;
      $data['sms_alert_number'] = '';
    }

    /*
     * Lastly, we need to check if the use_invoice_details flag is set. If not present then need to update the field
     */
    if (empty($data['use_invoice_details'])) {
      $data['use_invoice_details'] = false;
    }

    if (empty($data[''])) {
      $data['booking_form'] = false;
    }

    if (isset($data['languages_spoken']) && is_array($data['languages_spoken'])) {
      // Convert the urls field to an array.
      $registry = new JRegistry;
      $registry->loadArray($data['languages_spoken']);
      $data['languages_spoken'] = (string) $registry;
    }
    
    /*
     * SMS notification prefs are currently set in the property versions save method.
     * TO DO - Move this update to the property model/table.
     */


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
    $query->select('sms_alert_number, sms_valid, sms_status, sms_validation_code,sms_nightwatchman');
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
