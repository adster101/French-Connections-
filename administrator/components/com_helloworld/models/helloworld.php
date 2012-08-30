<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
/**
 * HelloWorld Model
 */
class HelloWorldModelHelloWorld extends JModelAdmin
{
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_helloworld.message.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
	public function getTable($type = 'HelloWorld', $prefix = 'HelloWorldTable', $config = array()) 
	{
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
	public function getForm($data = array(), $loadData = true) 
	{	

		// Get the form.
		$form = $this->loadForm('com_helloworld.helloworld', 'helloworld', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		
		return $form;
	}
  
  /*
   * Method to get a form for the user to choose which property they would like to add a unit to
   * 
   */
	public function getNewPropertyForm($data = array(), $loadData = false) 
	{	
    
		// Get the form.
		$form = $this->loadForm('com_helloworld.userproperties', 'userproperties', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		} 
    return $form;
  }
  
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'administrator/components/com_helloworld/models/forms/helloworld.js';
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
		$data = JFactory::getApplication()->getUserState('com_helloworld.edit.helloworld.83', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}	
  
  /* 
   * Method to set the parent property ID if it has been passed via POST
   * 
   * 
   */

	protected function preprocessForm(JForm $form, $data)
	{
    
    // Use getItem to get the data for the parent property if supplied

    $parent_id = JApplication::getUserState('parent_id');
    
    if (!isset($data->parent_id)){echo"Woot!";}
    
    
    // If parent_id is already set in data (and not 1) then use that value
    // Else if parent_id is set in userState then use that
    // Else set parent ID to 1
    
    if (isset($data->parent_id) && $data->parent_id != 1 && $data->parent_id != '') {
      
      // Grab the parent property details
      $parent_prop = $this->getItem( $data->parent_id );
      
      // Set the location details accordingly
      $data->latitude = $parent_prop->latitude;
      $data->longitude = $parent_prop->longitude;
      $data->nearest_town = $parent_prop->nearest_town;
      
      foreach($form->getFieldSet('Location') as $field) {
				// So we loop over the fields disabling them and making them non-required in the form
				// This ensure that they will not be editable by the user in this instance. 
				$form->setFieldAttribute(str_replace(array('jform','[',']'), '', $field->name), 'class', 'readonly');
				$form->setFieldAttribute(str_replace(array('jform','[',']'), '', $field->name), 'readonly', 'readonly');
				$form->setFieldAttribute(str_replace(array('jform','[',']'), '', $field->name), 'required', 'false');

      }		
    
    } else if (!isset($data->parent_id) && $parent_id != '') {
      $parent_prop = $this->getItem( $parent_id );
      
      // Set the location details accordingly
      $data->latitude = $parent_prop->latitude;
      $data->longitude = $parent_prop->longitude;
      $data->nearest_town = $parent_prop->nearest_town;
     
    } else {
      
    }
    

      
		// If the parent_id is not 1
		// !== compares the type as well as the value
		if($parent_id !=1 && $parent_id !='') {		
			// Then this property must be designated as a unit
 			foreach($form->getFieldSet('Location') as $field) {
				// So we loop over the fields disabling them and making them non-required in the form
				// This ensure that they will not be editable by the user in this instance. 
				//$form->setFieldAttribute(str_replace(array('jform','[',']'), '', $field->name), 'disabled', 'true');
				//$form->setFieldAttribute(str_replace(array('jform','[',']'), '', $field->name), 'required', 'false');
			}		
		}
    

    
	}
}
