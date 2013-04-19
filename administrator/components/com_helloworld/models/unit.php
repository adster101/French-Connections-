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

  /*
   * Function to get the data for the facilities editing view.
   * 
   * TODO - For a slight performance boost should consider implementing preprocessForm to generate the property attributes here.
   * Presntly, the facilities checkbox fields are loaded in via a custom form field type (facilities). 
   * This should probably be amended so that the checkbox options are added dynamically in a precpreocessform method in this model.
   * 
   * The above would be better as you could generate all the facility options via one query rather than five.
   * 
   */

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
    $attributesTable = $this->getTable('PropertyAttributes', 'HelloWorldTable');

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
    if (!empty($property_facilities)) {
      foreach ($property_facilities as $facility_type => $value) {
        $properties[$facility_type] = implode($value, ',');
      }
    }

    $item = JArrayHelper::toObject($properties, 'JObject');

    return $item;
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
  public function getUnits() {

    // Get the listing ID the user is editing against
    $id = JApplication::getUserState('com_helloworld.listing_id');

    // Get the units table
    $units_table = $this->getTable('PropertyUnits', 'HelloWorldTable');

    // Set the primary key to be the parent ID column, this allow us to fetch the units for this listing ID.
    $units_table->set('_tbl_key', 'parent_id');

    if ($id > 0) {
      // Attempt to load the row.
      $return = $units_table->load($id);

      // Check for a table object error.
      if ($return === false && $units_table->getError()) {
        $this->setError($units_table->getError());
        return false;
      }
    }

    // Convert to the JObject before adding other data.
    $properties = $units_table->getProperties(1);
    $units = JArrayHelper::toObject($properties, 'JObject');

    return $units;
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
   * Method to test whether a user can edit state.
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
  protected function populateState($ordering = null, $direction = null) {

    $canDo = HelloWorldHelper::getActions();
    $this->setState('actions.permissions', $canDo);

    // List state information.
    parent::populateState();
  }

  protected function preprocessForm(JForm $form, $data) {

    // Get the user
    $user = JFactory::getUser();

    // Set the default owner to the user creating this.
    $form->setFieldAttribute('created_by', 'type', 'hidden');
    $form->setFieldAttribute('created_by', 'default', $user->id);

    // We also need to get the listing ID from the session so we can associate this unit with a listing...
    $listing = JApplication::getUserState('listing', false);

    $form->setFieldAttribute('parent_id', 'default', $listing->id);
  }

  /**
   * Overidden method to save the form data.
   *
   * @param   array  $data  The form data.
   *
   * @return  boolean  True on success, False on error.
   *
   * @since   12.2
   */
  public function save($data) {
    $dispatcher = JEventDispatcher::getInstance();
    $table = $this->getTable();
    $key = $table->getKeyName();
    $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
    $isNew = true;

    // Include the content plugins for the on save events.
    JPluginHelper::importPlugin('content');

    // Allow an exception to be thrown.
    try {

      // If $data['new_version'] is true we need to update that new version in the version table
      if ($data['new_version']) {

        // Get the latest unpublished version id for this unit, that exists in the db
        $version = $this->getLatestUnitVersion($data['id']);

        if ($version->version_id > 0) {
          // Now we are ready to save our updated unit details to the new version table
          $table = $this->getTable('PropertyUnitsVersion');
          
          $table->set('_tbl_key','version_id');

          // Set the version ID that we want to bind and store the data against...
          $table->version_id = $version->version_id;
        }
      } else { // Here we don't explicitly know if there is a new version
        // Load the exisiting row, if there is one. 
        if ($pk > 0) {
          $table->load($pk);
          $isNew = false;
        }

        // Let's have a before bind trigger
        $new_version_required = $dispatcher->trigger('onContentBeforeBind', array($this->option . '.' . $this->name, $table, $isNew, $data));

        // $version should contain an array with one element. If the array contains true then we need to create a new version...
        if ($new_version_required[0]) {
          // Switch the table model to the version one
          $table = $this->getTable('PropertyUnitsVersion');
          $table->set('_tbl_key','version_id');

        }
      }

      
      // Bind the data.
      if (!$table->bind($data)) {
        $this->setError($table->getError());
        return false;
      }

      // Prepare the row for saving
      $this->prepareTable($table);

      // Check the data.
      if (!$table->check()) {
        $this->setError($table->getError());
        return false;
      }

      // Trigger the onContentBeforeSave event.
      $result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));

      if (in_array(false, $result, true)) {
        $this->setError($table->getError());
        return false;
      }

      // Store the data.
      if (!$table->store()) {
        $this->setError($table->getError());
        return false;
      } else {
        
        if($new_version_required[0]) {
          
          // Update the unit to indicate that it has been updated...
          // Update the property to indicate that one of it's units has been update...
          // This should only happen the first time we create a new version of this unit...
          $table = $this->getTable();
          
        }
         
      }

      // Should have a new unit version here.
      // Need to update the new_version flag in the #__property_units table to indicate a new, unpublished version
      // Also, need to save the facilities into a new table rather than saving them directly.
      // Save the facilities data...
      if (!$this->savePropertyFacilities($data, $pk)) {
        $this->setError('Problem saving facilities');
      }


      // Set the table key back to ID so the controller redirects to the right place
      $table->set('_tbl_key','id');

      // Need to update the original unit to indicate that it has a new, unpublished version...
      
      
      // Clean the cache.
      $this->cleanCache();

      // Trigger the onContentAfterSave event.
      $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
      
    } catch (Exception $e) {
      $this->setError($e->getMessage());

      return false;
    }

    $pkName = $table->getKeyName();

    if (isset($table->$pkName)) {
      $this->setState($this->getName() . '.id', $table->$pkName);
    }
    $this->setState($this->getName() . '.new', $isNew);

    return true;
  }

  /*
   * Method to get the version id of the most recent unpublished version
   * 
   * 
   */

  public function getLatestUnitVersion($id = '') {
    // Retrieve latest unit version
    $db = $this->getDbo();
    $query = $db->getQuery(true);
    $query->select('version_id');
    $query->from('#__property_units_versions');
    $query->where('id = ' . (int) $id);
    $query->where('state = 1');
    $query->order('version_id', 'desc');

    $db->setQuery((string) $query);

    try {
      $row = $db->loadObject();
    } catch (RuntimeException $e) {
      JError::raiseError(500, $e->getMessage());
    }

    return $row;
  }

  /*
   * Method to save the property attributes into the #__attribute_property table.
   * 
   * 
   * 
   */

  protected function savePropertyFacilities($data = array(), $id = 0) {

    if (!is_array($data) || empty($data)) {
      return true;
    }

    $attributes = array();

    // For now whitelist the attributes that are supposed to be processed here...needs moving to the model...or does it?
    $whitelist = array('external_facilities', 'internal_facilities', 'kitchen_facilities', 'activities', 'suitability');

    // Loop over the data and prepare an array to save
    foreach ($data as $key => $value) {

      if (!in_array($key, $whitelist)) {
        continue;
      }

      // We're not interested in the 'other' fields E.g. external_facilities_other
      if (strpos($key, 'other') == 0 && !empty($value)) {

        // Location, property and accommodation types are all single integers and not arrays 
        if (is_array($value)) {
          // We want to save this in one go so we make an array
          foreach ($value as $facility) {
            // Facilities should be integers
            if ((int) $facility) {
              $attributes[] = $facility;
            }
          }
        } else {
          $attributes[] = $value;
        }
      }
    }

    // If we have any attributes
    if (count($attributes) > 0) {

      // Get instance of the tariffs table
      $attributesTable = JTable::getInstance($type = 'PropertyAttributes', $prefix = 'HelloWorldTable', $config = array());

      // Bind the translated fields to the JTable instance	
      if (!$attributesTable->save($id, $attributes)) {
        JApplication::enqueueMessage(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_ADDING_ATTRIBUTES'), 'warning');

        JError::raiseWarning(500, $attributesTable->getError());
        return false;
      }

      return true;
    }
  }

}
