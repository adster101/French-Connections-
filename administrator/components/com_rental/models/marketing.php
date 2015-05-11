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
    
    if (!$model->save($data))
    {
      // TO DO - Need to go trhough the property versions save model and throw exceptions rather than returing false.
      $error = $model->getError();

      $this->setError($error);
      return false;
    }

    return true;
  }
  
 public function preprocessForm(\JForm $form, $data, $group = 'content')
  {

    
    $model = JModelLegacy::getInstance('Units', 'RentalModel', $config = array('ignore_request' => true));
    $model->setState('filter.search', $data->property_id);

    $units = $model->getItems();

    $addform = new SimpleXMLElement('<form />');
    $fields = $addform->addChild('fields');
    $fields->addAttribute('name', 'units');
    $fieldset = $fields->addChild('fieldset');
    $fieldset->addAttribute('name', 'availability-calendar');
    $fieldset->addAttribute('description', 'COM_RENTAL_MARKETING_AVAILABILITY_TARIFFS_FIELDSET_DESCRIPTION');
    $fieldset->addAttribute('label', 'COM_RENTAL_MARKETING_AVAILABILITY_TARIFFS_FIELDSET_LABEL');

    foreach ($units as $unit)
    {
      // Replace spaces with dashes as per the search component
      $value = '<iframe src=\'http://www.frenchconnections.co.uk/listing/' . $unit->property_id . '?unit_id=' . $unit->unit_id . '&view=availability&tmpl=component\' frameborder=\'0\'></iframe>';
      $field = $fieldset->addChild('field');
      $field->addAttribute('name', strtolower($unit->unit_id));
      $field->addAttribute('type', 'textarea');
      $field->addAttribute('label', $unit->unit_title);
      $field->addAttribute('multiple', true);
      $field->addAttribute('default', $value );
      $field->addAttribute('filter', 'unset');
      $field->addAttribute('rows', '3');
      $field->addAttribute('class', 'input-xxlarge');
    }

    $form->load($addform);
  }
}