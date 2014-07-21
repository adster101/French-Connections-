<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modeladmin library
// If we implement populateState and getItem then we extend directly from JModelForm
jimport('joomla.application.component.modeladmin');

class RentalModelMarketing extends JModelAdmin
{

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
    $form = $this->loadForm('com_rental.marketing', 'marketing', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
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
  public function getTable($type = 'PropertyVersions', $prefix = 'RentalTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
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
    $data = JFactory::getApplication()->getUserState('com_rental.edit.marketing.data', array());

    if (empty($data))
    {
      $data = $this->getItem();
    }

    return $data;
  }

  /**
   * Overriden save method. Use the propertyversions model to save the data...
   * Verify the SMS number is also handled here.
   * TO DO - Get the SMS details up front. Then add a check to see if mobile number is set.
   * If the mobile number is different then trigger the validation process again (resetting all)
   * If empry then clear all and reset.
   *  
   */
  public function save($data)
  {

    $params = JComponentHelper::getParams('com_rental');

    // Call populate state here otherwise the controller doesn't know where to redirect to 
    // Ordinarily this would be called in the models parent save method.
    $state = $this->populateState();

    /*
     * Get the property versions model
     */
    $model = JModelLegacy::getInstance('PropertyVersions', 'RentalModel');
    
    // Update the various additional marketing bits
    $data['lwl'] = ($data['lwl']) ? $data['lwl'] : '';
    $data['frtranslation'] = ($data['frtranslation']) ? $data['frtranslation'] : '';
    
    if (!$model->save($data))
    {
      // TO DO - Need to go trhough the property versions save model and throw exceptions rather than returing false.
      $error = $model->getError();

      $this->setError($error);
      return false;
    }

    return true;
  }

}