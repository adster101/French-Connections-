<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class HelloWorldModelUnitVersions extends JModelAdmin {

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
  public function getTable($type = 'UnitVersions', $prefix = 'HelloWorldTable', $config = array()) {
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
  public function populateState($ordering = null, $direction = null) {

    $canDo = HelloWorldHelper::getActions();
    $this->setState('actions.permissions', $canDo);

    // Set the model state for this unit
    $app = JFactory::getApplication();
    $input = $app->input;

    $listing_id = $input->get('listing_id', '', 'int');
    $this->setState('unitversions.listing_id', $listing_id);


    // List state information.
    parent::populateState();
  }

  protected function preprocessForm(JForm $form, $data) {

    // Get the user
    $user = JFactory::getUser();

    $app = JFactory::getApplication();
    $input = $app->input;
    $parent_id = $input->get('parent_id', '', 'int');

    // Set the parent ID for this unit, if it's not set
    if (empty($data->parent_id)) {

      $data->parent_id = $parent_id;

      $form->setFieldAttribute('parent_id', 'default', $parent_id);
    }

    if (!empty($data)) { // Only applies when populating the form, data not present when validating.
      $form->setFieldAttribute('parent_id', 'default', $data->parent_id);
    }

    // Also need to check edit.state permission and unset published field if not allowed.
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
    $new_version_required = array('');

    $old_version_id = ($data['id']) ? $data['id'] : '';

    // Include the content plugins for the on save events.
    JPluginHelper::importPlugin('content');

    // Allow an exception to be thrown.
    try {

      // If $data['review'] is not true we need to load the existing data to be able to compare
      if ($data['review'] == 0) {

        // Here we don't explicitly know if there is a new version
        // Load the exisiting row, if there is one.
        if ($pk > 0) {
          $table->load($pk);
          $isNew = false;
        }

        // Let's have a before bind trigger
        $new_version_required = $dispatcher->trigger('onContentBeforeBind', array($this->option . '.' . $this->name, $table, $isNew, $data));

        // $version should contain an array with one element. If the array contains true then we need to create a new version...
        if ($new_version_required[0] === true) {
          // As a new version is required amend the data array before we save
          $data['id'] = '';
          $data['review'] = '1';
          $data['published_on'] = '';
        }
      }

      // If this is a new unit then we need to generate a 'stub' entry into the unit table
      // which essentially handles the non versionable stuff (like expiry data, ordering and published state).
      if ($isNew) {

        $data['property_id'] = $data['parent_id'];
        $new_unit_id = $this->createNewUnit($data);

        if (!$new_unit_id) {

          // Problem creating the new property stub...
          $this->setError('There was a problem createing your unit. Please try again.');
          return false;
        } else {
          $data['review'] = '1';
          $data['published_on'] = '';
          $data['unit_id'] = $new_unit_id;
        }
      }

      // Set the table model to the appropriate key
      // If we don't do this, the model will save against the parent_id
      // but we want it saving against the version id
      $table->set('_tbl_key', 'id');

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

        // Update the existing property listing to indicate that we have a new version for it.
        $property = $this->getTable('Property', 'HelloWorldTable');

        $property->id = $table->unit_id;
        $property->review = 1;

        if (!$property->store()) {
          $this->setError($property->getError());
          return false;
        }
      }

      $new_version_id = ($table->id) ? $table->id : '';

      // Here we may have created a new version.
      // If so then we need to save the facilities against the new version
      // create a copy of all images against the new version
      // and potentially make a copy of the availability and tariffs (although this may be deferred).

      if (!$this->savePropertyFacilities($data, $property->id, $new_version_id)) {
        $this->setError('Problem saving facilities');
      }

      // Wrap this up into a neat function
      // When a new version is created we also need to take all existing images and copy 'em

      if ($new_version_required[0] === true) {

        $image = JModelLegacy::getInstance('Images', 'HelloWorldModel');

        $image->setState('listlimit', '10000');

        $image->setState('version_id', $old_version_id);

        $images = $image->getItems();

        // Shove these images into the images table against $new_version_id
      }






      // Clean the cache.
      $this->cleanCache();

      // Trigger the onContentAfterSave event.
      $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
    } catch (Exception $e) {

      $this->setError($e->getMessage());
      return false;
    }

    // Set the table key back to the parent id so it redirects based on that key
    // on not the version key id
    $table->set('_tbl_key', 'unit_id');

    $pkName = $table->getKeyName();

    if (isset($table->$pkName)) {
      $this->setState($this->getName() . '.id', $table->$pkName);
    }
    $this->setState($this->getName() . '.new', $isNew);

    return true;
  }

  /*
   * Method to save the property attributes into the #__attribute_property table.
   *
   *
   *
   */

  protected function savePropertyFacilities($data = array(), $id = 0, $version_id = '') {

    if (!is_array($data) || empty($data)) {
      return true;
    }

    if (empty($version_id)) {
      return true;
    }

    $attributes = array();

    // For now whitelist the attributes that are supposed to be processed here...access options need adding.
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
      if (!$attributesTable->save($id, $attributes, $version_id)) {
        JApplication::enqueueMessage(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_ADDING_ATTRIBUTES'), 'warning');

        JError::raiseWarning(500, $attributesTable->getError());
        return false;
      }

      return true;
    }
  }

  /*
   *
   * Method to create a 'unit' entry into the #__unit table.
   * This needs to be done prior to saving the version into #__unit_versions for new props
   *
   */

  public function createNewUnit($data = array()) {

    if (empty($data)) {
      return false;
    }

    $unit_table = $this->getTable('Unit', 'HelloWorldTable');

    if (!$unit_table->bind($data)) {
      $this->setErrro($unit_table->getError());
      return false;
    }

    // Optional further sanity check after data has been validated, filtered, and about to be checked...
    //$this->prepareTable($property_table);
    if (!$unit_table->store()) {
      return false;
    }

    return $unit_table->id;
  }

}