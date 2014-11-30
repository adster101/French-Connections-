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
    
    /*
     * Get the property versions model
     */
    $model = JModelLegacy::getInstance('PropertyVersions', 'RentalModel');

    /*
     * We need to check if the use_invoice_details flag is set. If not present then need to update the field.
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
}

