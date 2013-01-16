<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class HelloWorldModelFacilities extends JModelAdmin {

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
  public function getTable($type = 'HelloWorld', $prefix = 'HelloWorldTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
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
  public function getAttributesTable($type = 'PropertyAttributes', $prefix = 'HelloWorldTable', $config = array()) {
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
    $form = $this->loadForm('com_helloworld.facilities', 'facilities', array('control' => 'jform', 'load_data' => $loadData));
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
    $data = JFactory::getApplication()->getUserState('com_helloworld.edit.facilities.data', array());

    if (empty($data)) {
      $data = $this->getItem();
    }
    return $data;
  }  

  public function getItem($pk = null) {

    // Initialise variables.
    $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

    $table = $this->getTable();

    $property_facilities = '';

    if ($pk > 0) {
      // Attempt to load the row.
      $return = $table->load($pk);

      // Check for a table object error.
      if ($return === false && $table->getError()) {
        $this->setError($table->getError());
        return false;
      }
    }

    // Convert to the JObject before adding other data.
    $properties = $table->getProperties(1);

    // Get an instance of the attributes table - Possibly need to merge this into com_attributes
    $attributesTable = $this->getAttributesTable();

    if ($pk > 0) {
      $property_facilities = $attributesTable->load($id = $pk);

      // Check for a table object error.
      if ($property_facilities === false && $attributesTable->getError()) {
        $this->setError($attributesTable->getError());
        return false;
      }
    }

    // Load returns an array for each facility type
    // We need to append each one to item so that they may be bound to the form
    foreach ($property_facilities as $facility_type=>$value) {
      $properties[$facility_type] = implode($value,',');
    }
    
    
    $item = JArrayHelper::toObject($properties, 'JObject');

    return $item;
  }

  /**
   * Used as a callback for array_map, turns the multi-file input array into a sensible array of files
   * Also, removes illegal characters from the 'name' and sets a 'filepath' as the final destination of the file
   *
   * @param	string	- file name			($files['name'])
   * @param	string	- file type			($files['type'])
   * @param	string	- temporary name	($files['tmp_name'])
   * @param	string	- error info		($files['error'])
   * @param	string	- file size			($files['size'])b
   *
   * @return	array
   * @access	protected
   */
  protected function reformatFilesArray($caption, $name) {
    $name = JFile::makeSafe($name);
    return array(
        'attribute_type_id' => $caption,
    );
  }

  /**
   * Method to test whether a user can edit the published state of a property.
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
    } else if ($task[1] == 'publish' || $task[1] == 'unpublish') {
      return $user->authorise('helloworld.edit.publish', $this->option);
    } else if ($task[1] == 'trash') {
      return $user->authorise('helloworld.edit.trash', $this->option);
    } else {
      return false;
    }
  }

}
