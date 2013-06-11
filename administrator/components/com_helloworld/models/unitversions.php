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
   * If no form data is available then we set the parent_id using the
   * request data.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData() {

    $input = JFactory::getApplication()->input;
    $parent_id = $input->get('parent_id', '', 'int');

    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_helloworld.edit.unitversions.data', array());


    // If nout in session then we grab the item from the database
    if (empty($data)) {
      $data = $this->getItem();
    }

    // If data is not an array convert it from object
    if (!is_array($data)) {
      $data = $data->getProperties();
    }

    // Set the parent ID for this unit, if it's not set (e.g. for a new unit)
    if (!isset($data['parent_id'])) {
      $data['parent_id'] = $parent_id;
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

    // Generate a logger instance for reviews
    JLog::addLogger(array('text_file' => 'unitversions.update.php'), JLog::ALL, array('unitversions'));

    // Get an db instance and start a transaction
    $db = JFactory::getDBO();
    $db->transactionStart();

    // Include the content plugins for the on save events.
    JPluginHelper::importPlugin('content');

    // Allow an exception to be thrown.
    try {

      // Here we don't explicitly know if there is a new version
      // Load the exisiting row, if there is one.

      if ($pk > 0) {
        $table->load($pk);
        $isNew = false;
      }

      // Check whether this unit is published, if it is then we run the check to see if a new version is required.
      if (!empty($data['published_on'])) {
        JLog::add('Checking if new unit version is needed for ' . $pk, JLog::ALL, 'unitversions');

        // Let's have a before bind trigger
        $new_version_required = $dispatcher->trigger('onContentBeforeBind', array($this->option . '.' . $this->name, $table, $isNew, $data));

        // $version should contain an array with one element. If the array contains true then we need to create a new version...
        if ($new_version_required[0] === true) {
          // As a new version is required amend the data array before we save
          $data['id'] = '';
          // Don't think that a review state is needed here.
          // Will always be set in the unit stub if it needs reviewing
          // $data['review'] = '0';
          $data['published_on'] = '';
          JLog::add('New unit version is needed for ' . $pk, JLog::ALL, 'unitversions');
        }
      }

      // If this is a new unit then we need to generate a 'stub' entry into the unit table
      // which essentially handles the non versionable stuff (like expiry data, ordering and published state).
      if ($isNew) {

        // in unit table property ID refers to the parent property
        $data['property_id'] = $data['parent_id'];
        $data['review'] = 1;
        $new_unit_id = $this->createNewUnit($data);


        if (!$new_unit_id) {
          // Problem creating the new property stub...
          Throw New Exception(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_SAVING_UNIT'));
        }

        JLog::add('New unit ' . $new_unit_id . 'created', JLog::ALL, 'unitversions');

        // Set the new unit id in the data array so that
        // when it is bound below it is assign to the correct property
        $data['unit_id'] = $new_unit_id;
      }

      // Set the table model to the appropriate key
      // If we don't do this, the model will save against the parent_id
      // but we want it saving against the version id
      $table->set('_tbl_key', 'id');

      // Bind the data.
      if (!$table->bind($data)) {
        $this->setError($table->getError());
        Throw New Exception(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
      }

      // Prepare the row for saving
      $this->prepareTable($table);

      // Check the data. Use this to increment the counter for unit?
      if (!$table->check()) {
        $this->setError($table->getError());
        Throw New Exception(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
      }

      // Trigger the onContentBeforeSave event.
      $result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));

      if (in_array(false, $result, true)) {
        $this->setError($table->getError());
        Throw New Exception(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
      }

      // Store the data.
      if (!$table->store()) {
        $this->setError($table->getError());
        Throw New Exception(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
      }

      // The version id is the id of the version created/updated in the _unit_versions table
      $new_version_id = ($table->id) ? $table->id : '';

      // We will always want to update the facilities relating to the version id
      // E.g. if a new unit, insert facilitites, if new version then we will
      // save the facilities against the new version id.

      JLog::add('About to save facilities for unit version ID' . $new_version_id, JLog::ALL, 'unitversions');

      if (!$this->savePropertyFacilities($data, $table->unit_id, $old_version_id, $new_version_id)) {
        Throw New Exception(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
      }

      // When a new version is created or a new unit is created
      if ($new_version_required[0] === true || $isNew) {

        // Here we have created a new version or a new unit
        // TO DO: Wrap the below into a function
        // Update the existing property listing to indicate that it has been modified in a way that requires a review
        $property = $this->getTable('Property', 'HelloWorldTable');

        $property->id = $table->parent_id;
        $property->review = 1;
        JLog::add('About to update Property review status for ' . $property->id, 'unitversions');

        if (!$property->store()) {
          $this->setError($property->getError());
          Throw New Exception(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
        }

        if (!$isNew) { // For a new unit the review status defaults to 1 so no need to update here
          // Update the existing unit listing to indicate that it has been modified (e.g. a new one created)
          $unit = $this->getTable('Unit', 'HelloWorldTable');
          $unit->id = $pk;
          $unit->review = 1;

          JLog::add('About to update Unit review status for ' . $pk, 'unitversions');

          if (!$unit->store()) {
            $this->setError($unit->getError());
            Throw New Exception(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
          }

          // Copy the images against the new version id, but only if the versions are different
          // If we are updating a new unpublished version, no need to copy images

          if ($old_version_id != $new_version_id) {
          JLog::add('About to copy images for unit ' . $pk, 'unitversions');

            $this->copyUnitImages($old_version_id, $new_version_id);
          }
        }
      }

      // Clean the cache??
      $this->cleanCache();

      // Commit the transaction
      $db->transactionCommit();

      // Trigger the onContentAfterSave event.
      $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
    } catch (Exception $e) {

      // Roll back any queries executed so far
      $db->transactionRollback();
      print_r($e->getMessage());
      die;
      $this->setError($e->getMessage());

      // Log the exception
      JLog::add('There was a problem: ' . $e->getMessage(), JLog::ALL, 'unitversions');
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

  public function copyUnitImages($old_version_id = '', $new_version_id = '') {

    // Get a list of all images stored against the old version
    $image = JModelLegacy::getInstance('Images', 'HelloWorldModel');
    $image->setState('listlimit', '10000');
    $image->setState('version_id', $old_version_id);

    // Get the images assigned to this old unit version id
    $images = $image->getItems();

    // Get a db instance
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->insert('#__property_images_library');

    $query->columns(array('version_id', 'property_id', 'image_file_name', 'caption', ordering));

    foreach ($images as $image) {
      // Only insert if there are some images
      $insert_string = "$new_version_id, '" . $image->property_id . "','" . $image->image_file_name . "','" . mysql_real_escape_string($image->caption) . "','" . $image->ordering . "'";
      $query->values($insert_string);
    }

    // Execute the query
    $this->_db->setQuery($query);

    if (!$db->execute($query)) {
      Throw New Exception(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));

    }
    return true;



  }

  /*
   * Method to save the property attributes into the #__attribute_property table.
   *
   *
   *
   */

  protected function savePropertyFacilities($data = array(), $id = 0, $old_version_id = '', $new_version_id = '') {

    if (!is_array($data) || empty($data)) {
      return true;
    }

    if (empty($old_version_id) || empty($new_version_id)) {
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
      $attributesTable = JTable::getInstance('PropertyAttributes', 'HelloWorldTable', $config = array());

      // Bind the translated fields to the JTable instance
      if (!$attributesTable->save($id, $attributes, $old_version_id, $new_version_id)) {
        JApplication::enqueueMessage(JText::_('COM_HELLOWORLD_HELLOWORLD_PROBLEM_ADDING_ATTRIBUTES'), 'warning');

        JError::raiseWarning(500, $attributesTable->getError());
        return false;
      }

      return true;
    }

    return true;
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
