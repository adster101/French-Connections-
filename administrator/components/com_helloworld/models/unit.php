<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class HelloWorldModelUnit extends JModelAdmin {

  /**
   * Method override to check if you can edit an existing record.
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   * @since	1.6
   */
  public function allowEdit($data = array(), $key = 'id') {
    // Check specific edit permission then general edit permission.
    return JFactory::getUser()->authorise('core.edit', 'com_helloworld.message.' . ((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
  public function getTable($type = 'PropertyUnits', $prefix = 'HelloWorldTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }
  
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
    
		$table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		} else {
      return false;
    }


		return $return;
	}
  
	/**
	 * Method to get a list of units for a given property listing
	 *
	 * @param   integer  $pk  The id of the property listing.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getUnits()
	{
    
    $id = $this->getState('unit.parent_id', '');

    // Get the units table
    $units_table = $this->getTable('PropertyUnits','HelloWorldTable');

    if ($id > 0)
		{
			// Attempt to load the row.
			$return = $units_table->load_units($id);

			// Check for a table object error.
			if ($return === false && $units_table->getError())
			{
				$this->setError($units_table->getError());
				return false;
			}
		} else {
      return false;
    } 
    
        
    $units = $return;

		return $units;
	}
  
  public function getProgress()
  {
    $input= JFactory::getApplication()->input;
    $id = $input->get('id', '', 'int');
    
    // Get the units table
    $units_table = $this->getTable('PropertyUnits','HelloWorldTable'); 
    
    $return = false;
    
    if ($id)
		{
			// Attempt to load the row.
			$return = $units_table->progress($id);

			// Check for a table object error.
			if ($return === false && $units_table->getError())
			{
				$this->setError($units_table->getError());
				return false;
			}
		}    
    
    return $return;
   

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
    $form = $this->loadForm('com_helloworld.unit', 'unit', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }

    return $form;
  }
  

  /*
   * Method to get a form for the user to choose which property they would like to add a unit to
   * 
   */

  public function getNewPropertyForm($data = array(), $loadData = false) {

    // Get the form.
    $form = $this->loadForm('com_helloworld.userproperties', 'userproperties', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }
    return $form;
  }
  
  /*
   * Method to get a form for the admin user to choose which account they would like to add a property to
   * 
   */

  public function getNewAdminPropertyForm($data = array(), $loadData = false) {

    // Get the form.
    $form = $this->loadForm('com_helloworld.addpropertybyuser', 'addpropertybyuser', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }
        
    return $form;
  }
  
  /**
   * Method to get the script that have to be included on the form
   *
   * @return string	Script files
   */
  public function getScript() {
    return 'administrator/components/com_helloworld/models/forms/helloworld.js';
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_helloworld.edit.unit.data', array());

    if (empty($data)) {
      $data = $this->getItem();
    }
    
    
    return $data;
  }



  /**
   * Method to test whether a record can be deleted.
   *
   * @param   object  $record  A record object.
   *
   * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
   *
   * @since   11.1
   */
  protected function canEditState() {
    $comtask = JRequest::getVar('task', '', 'POST', 'string');

    $task = explode('.', $comtask);

    $user = JFactory::getUser();

    if ($task[1] == 'orderdown' || $task[1] == 'orderup') {
      return $user->authorise('helloworld.edit.reorder', $this->option);
    } else if ($task[1] == 'publish' || $task[1] == 'unpublish' || $task[1] == 'trash') {
      return $user->authorise('core.edit.state', $this->option);
    } else {
      return false;
    }
  }
  
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param	string	An optional ordering field.
	 * @param	string	An optional direction (asc|desc).
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{

		$canDo = HelloWorldHelper::getActions();
		$this->setState('actions.permissions', $canDo);
		
		// List state information.
		parent::populateState();
	}
  
}
