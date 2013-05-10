<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class HelloWorldModelProperty extends JModelAdmin {

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'PropertyVersions', $prefix = 'HelloWorldTable', $config = array()) {
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
    $form = $this->loadForm('com_helloworld.property', 'property', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }

    return $form;
  }

  /**
   * getItem is overridden so that we can, if necessary, load any updated fields from the version tables.
   *
   * We check the review state flag and if that is true, we load property details from the #__property_listings_versions table and update the fields
   *
   * @param   integer  $pk  The id of the primary key.
   *
   * @return  mixed    Object on success, false on failure.
   *
   * @since   12.2
   */
  public function getItem($pk = null) {
    $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
    $table = $this->getTable();

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

    // If review flag is true, there is an unpublished version in the versions table.
    if ($properties['review']) {

      // Need to load the new version details here to replace those loaded here.
    }

    $item = JArrayHelper::toObject($properties, 'JObject');

    if (property_exists($item, 'params')) {
      $registry = new JRegistry;
      $registry->loadString($item->params);
      $item->params = $registry->toArray();
    }

    return $item;
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
   * Method to get a list of units for a given property listing
   *
   * @param   integer  $pk  The id of the property listing.
   *
   * @return  mixed    Object on success, false on failure.
   *
   * @since   12.2
   */
  public function getUnits() {

    $input = JFactory::getApplication()->input;
    $id = $input->get('id', '', 'int');

    $return = false;

    // Get the units table
    $units_table = $this->getTable('UnitVersions', 'HelloWorldTable');

    // Set the primary key to be the parent ID column, this allow us to fetch the units for this listing ID.
    $units_table->set('_tbl_key', 'parent_id');

    if ($id > 0) {
      // Attempt to load the row.
      $return = $units_table->load_units($id);

      // Check for a table object error.
      if ($return === false && $units_table->getError()) {
        $this->setError($units_table->getError());
        return false;
      }
    } else {
      return false;
    }

    // Convert to the JObject before adding other data.
    $units = JArrayHelper::toObject($return, 'JObject');

    return $units;
  }

  public function getProgress() {

    $return = false;

    // Get the units table
    $units_table = $this->getTable('PropertyUnits', 'HelloWorldTable');

    if ($id) {
      // Attempt to load the row.
      $return = $units_table->progress($id);

      // Check for a table object error.
      if ($return === false && $units_table->getError()) {
        $this->setError($units_table->getError());
        return false;
      }
    }

    return $return;
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_helloworld.edit.property.data', array());

    if (empty($data)) {
      $data = $this->getItem();
    }

    return $data;
  }

  /*
   * This method checks whether the property being edited is a unit.
   * If it is then we take the lat and long from the parent property
   * and force those to be the same for this property.
   *
   * This can happen from two places.
   * Firstly, if a user is adding a new property they may choose a parent property
   * in which case we take the parent_id from the user session.
   *
   * Secondly, if the user is editing an existing property which already has a
   * parent_id set. I.e. is already marked as a unit. In this case it will be set
   * in the $data scope.
   *
   * param JForm $form The JForm instance for the view being edited
   * param array $data The form data as derived from the view (may be empty)
   *
   * @return void
   *
   */

  protected function preprocessForm(JForm $form, $data) {

    // More robustly checked on the component level permissions?
    // E.g. at the moment any user who is not owner can edit this?
    // e.g. add a new permission core.edit.property.changeparent

    $canDo = $this->getState('actions.permissions', array());
    // If we don't come from a view then this maybe empty so we reset it.
    if (empty($canDo)) {
      $canDo = HelloWorldHelper::getActions();
    }

    // Check the change parent ability of this user
    if (!$canDo->get('helloworld.edit.property.owner')) {
      $user = JFactory::getUser();
      // Set the default owner to the user creating this.
      $form->setFieldAttribute('created_by', 'type', 'hidden');
      $form->setFieldAttribute('created_by', 'default', $user->id);
    }

    // Set the location details accordingly, needed for one of the form field types...
    if (!empty($data->latitude) && !empty($data->longitude)) {
      $form->setFieldAttribute('city', 'latitude', $data->latitude);
      $form->setFieldAttribute('city', 'longitude', $data->longitude);
    }
  }

  /**
   * Method to return the location details based on the city the user has chosen
   *
   * @param   int    $city, the nearest town/city
   *
   * @return  mixed
   *
   * @since   11.1
   */
  protected function getLocationDetails($city) {

    $location_details_array = array();

    // Get the table instance for the classification table
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');

    $table = $this->getTable('Classification', 'ClassificationTable');

    if (!$location_details = $table->getPath($city)) {
      $this->setError($table->getError());
      return false;
    };

    // Loop over the location details and pass them back as an array
    foreach ($location_details as $key => $value) {

      if ($value->level > 0) {
        $location_details_array[] = $value->id;
      }
    }


    return $location_details_array;
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
  protected function populateState($ordering = null, $direction = null) {

    $canDo = HelloWorldHelper::getActions();
    $this->setState('actions.permissions', $canDo);

    // List state information.
    parent::populateState();
  }

  /**
   * Overidden method to save the form data.
   *
   * @param   array  $data  The filtered form data.
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

    $city = (!empty($data['city'])) ? $data['city'] : '';

    // Get the location details (area, region, dept) and update the data array
    $location_details = $this->getLocationDetails($city);

    // Update the location details in the data array...ensures that property will always be in the correct area, region, dept, city etc
    if (!empty($location_details)) {
      $data['country'] = $location_details[0];
      $data['area'] = $location_details[1];
      $data['region'] = $location_details[2];
      $data['department'] = $location_details[3];
      $data['city'] = $location_details[4];
    }

    // Include the content plugins for the on save events.
    JPluginHelper::importPlugin('content');

    // Allow an exception to be thrown.
    try {

      // If $data['review'] is true we need to check whether a new version is required
      if ($data['review'] == 0) {

        // Load the exisiting row, if there is one.
        if ($pk > 0) {
          $table->load($pk);
          $isNew = false;
        }

        // Let's have a before bind trigger
        $new_version_required = $dispatcher->trigger('onContentBeforeBind', array($this->option . '.' . $this->name, $table, $isNew, $data));

        // $version should contain an array with one element. If the array contains true then we need to create a new version...
        if ($new_version_required[0]) {

          // As a new version is required amend the data array before we save
          $data['id'] = '';
          $data['review'] = '1';
          $data['published_on'] = '';
        }
      }

      // If this is a new propery then we need to generate a 'stub' entry into the propery table
      // which essentially handles the non versionable stuff (like expiry data, ordering and published state).
      if ($isNew) {

        $new_property_id = $this->createNewProperty($data);

        if (!$new_property_id) {

          // Problem creating the new property stub...
          $this->setError('There was a problem createing your property. Please try again.');
          return false;
        } else {
          $data['parent_id'] = $new_property_id;
        }
      }

      // Set the table model to the appropriate key
      // If we don't do this, the model will save against the parent_id
      // but we want it saving against the version id
      $table->set('_tbl_key', 'id');

      // Bind the data. This will bind to the appropriate table.
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
      }

      // If not a new property mark the property listing as for review
      // TO DO: look at this - ensure that new props can't be published without review
      if (!$isNew) {

        // Update the existing property listing to indicate that the listing has been updated
        $property = $this->getTable('Property', 'HelloWorldTable');

        $property->id = $table->parent_id;
        $property->review = 1;

        if (!$property->store()) {
          $this->setError($property->getError());
          return false;
        }
      }

      // Save any admin notes, if present
      if (!empty($data['note'])) {


        $note = array();

        $note['property_id'] = $data['parent_id'];
        $note['state'] = 1;
        $note['body'] = $data['note'];
        $note['created_time'] = JFactory::getDate()->toSql();

        // $this->saveAdminNote($note);

        $note_table = $this->getTable('Note', 'HelloWorldTable');

        // Bind the data.
        if (!$note_table->bind($note)) {
          $this->setError($note_table->getError());
          return false;
        }

        // Prepare the row for saving
        $this->prepareTable($note_table);

        // Check the data.
        if (!$note_table->check()) {
          $this->setError($note_table->getError());
          return false;
        }
        // Store the data.
        if (!$note_table->store()) {
          $this->setError($note_table->getError());
          return false;
        }
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
    $table->set('_tbl_key', 'parent_id');

    $pkName = $table->getKeyName();

    if (isset($table->$pkName)) {
      $this->setState($this->getName() . '.id', $table->$pkName);
    }
    $this->setState($this->getName() . '.new', $isNew);

    return true;
  }

  public function createNewProperty($data = array()) {

    if (empty($data)) {
      return false;
    }

    $property_table = $this->getTable('Property', 'HelloWorldTable');

    if (!$property_table->bind($data)) {
      $this->setErrro($property_table->getError());
      return false;
    }

    // Optional further sanity check after data has been validated, filtered, and about to be checked...
    //$this->prepareTable($property_table);
    if (!$property_table->store()) {
      return false;
    }

    return $property_table->id;
  }

}
