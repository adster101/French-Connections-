<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class RentalModelUnitVersions extends JModelAdmin
{

  public $layout = '';
  public $new_version_required = false;

  /**
   * Method override to check if you can edit an existing record.
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   * @since	1.6
   */
  public function allowEdit($data = array(), $key = 'id')
  {
    // Check specific edit permission then general edit permission.
    return JFactory::getUser()->authorise('core.edit', 'com_rental.message.' . ((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
  public function getTable($type = 'UnitVersions', $prefix = 'RentalTable', $config = array())
  {
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

  public function getItem($pk = null)
  {

    // Get the unit version detail.
    if ($item = parent::getItem($pk))
    {

      // Use the primary key (in this case unit id) to pull out any existing facilities for this property
      $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

      $attributes = $this->getFacilities($pk, $item->id);

      foreach ($attributes as $key => $values)
      {
        $item->$key = $values;
      }
      // Add any tariffs to the unit data for display on the view
    }

    //$item = JArrayHelper::toObject($properties, 'JObject');

    return $item;
  }

  /**
   * Get the tariffs for this unit
   * 
   */
  public function getFacilities($id, $version)
  {

    // Array to hold the result list
    $property_attributes = array();

    $properties = array();

    // Loads a list of the attributes that we are interested in
    // This is probably reused on the search part
    $query = $this->_db->getQuery(true);

    $query->select('d.field_name, b.attribute_id');
    $query->from('#__unit_attributes b');
    $query->join('left', '#__attributes c on c.id = b.attribute_id');

    $query->leftJoin('#__attributes_type d on d.id = c.attribute_type_id');

    $query->where($this->_db->quoteName('b.property_id') . ' = ' . (int) $id);

    $query->where($this->_db->quoteName('b.version_id') . ' = ' . (int) $version);

    $this->_db->setQuery($query);

    // Execute the db query, returns an iterator object.
    $result = $this->_db->getIterator();
    // Loop over the iterator and do stuff with it
    foreach ($result as $row)
    {

      $tmp = JArrayHelper::fromObject($row);

      // If the facility type already exists
      if (!array_key_exists($tmp['field_name'], $property_attributes))
      {
        $property_attributes[$tmp['field_name']] = array();
      }

      $property_attributes[$tmp['field_name']][] = $tmp['attribute_id'];
    }

    // Load returns an array for each facility type
    // We need to append each one to item so that they may be bound to the form
    if (!empty($property_attributes))
    {
      foreach ($property_attributes as $facility_type => $value)
      {
        $properties[$facility_type] = implode($value, ',');
      }
    }

    return $properties;
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

    $input = JFactory::getApplication()->input;
    $layout = $input->get('layout', 'edit', 'string');

    if ($this->layout == 'tariffs' || $layout == 'tariffs')
    {
      $form = $this->loadForm('com_rental.unit', 'tariffs', array('control' => 'jform', 'load_data' => $loadData));
    }
    else
    {
      $form = $this->loadForm('com_rental.unit', 'unit', array('control' => 'jform', 'load_data' => $loadData));
    }

    // Get the form.
    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  /**
   * Method to get the data that should be injected in the form.
   * If no form data is available then we set the property_id using the
   * request data.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData()
  {

    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_rental.edit.unitversions.data', array());


    // Need to get the tariff data into the form here...
    // If nout in session then we grab the item from the database
    if (empty($data))
    {
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
  protected function canEditState()
  {
    $comtask = JRequest::getVar('task', '', 'POST', 'string');

    $task = explode('.', $comtask);

    $user = JFactory::getUser();

    if ($task[1] == 'orderdown' || $task[1] == 'orderup')
    {
      return $user->authorise('helloworld.edit.reorder', $this->option);
    }
    else if ($task[1] == 'publish' || $task[1] == 'unpublish' || $task[1] == 'trash')
    {
      return $user->authorise('core.edit.state', $this->option);
    }
    else
    {
      return false;
    }
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
  public function save($data)
  {
    $dispatcher = JEventDispatcher::getInstance();
    $table = $this->getTable();
    $model = JModelLegacy::getInstance('Property', 'RentalModel', $config = array('ignore_request' => 'true'));
    $key = $table->getKeyName();
    $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
    $isNew = true;

    // Generate a logger instance for reviews
    JLog::addLogger(array('text_file' => 'unitversions.update.php'), 'DEBUG', array('unitversions'));

    // Get an db instance and start a transaction
    $db = JFactory::getDBO();
    $db->transactionStart();

    // Allow an exception to be thrown.
    try {

      // Here we don't explicitly know if there is a new version
      // Load the exisiting row, if there is one.

      if ($pk > 0)
      {
        $table->load($pk);
        $isNew = false;
      }

      $old_version_id = ($table->id) ? $table->id : '';

      // If this is a new unit then we need to generate a 'stub' entry into the unit table
      // which essentially handles the non versionable stuff (like expiry data, ordering and published state).
      // TO DO - Move this code to run when user chooses add new property
      if ($isNew)
      {

        // in unit table property ID refers to the parent listing id
        // $data['property_id'] = $data['property_id'];
        // Pass the review state in as an argument
        $data['review'] = 1;
        $new_unit_id = $this->createNewUnit($data);


        if (!$new_unit_id)
        {
          // Problem creating the new property stub...
          Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_UNIT'));
        }

        JLog::add('New unit ' . $new_unit_id . 'created', 'DEBUG', 'unitversions');

        // Set the new unit id in the data array so that
        // when it is bound below it is assign to the correct property
        // Set unit id as a model property?
        $data['unit_id'] = $new_unit_id;
      }

      // Check whether this unit is marked as needing a review, if not then we need to check if we should create a new version
      if (!($data['review']))
      {

        // Need to verify the expiry date for this property. If no expiry date then no new version is required.
        // New method - getPropertyDetails(); returns the expiry date of the property.
        $expiry_date = $model->getPropertyDetails($data['property_id']);

        if (is_integer($expiry_date))
        {
          // As a new version is required amend the data array before we save
          $data['id'] = '';
          $data['review'] = 1;
          $data['published_on'] = '';

          // Set the new version required flag to true
          $this->new_version_required = true;

          JLog::add('New unit version is needed for ' . $pk, 'DEBUG', 'unitversions');
        }
      }

      // Set the table model to the appropriate key
      // If we don't do this, the model will save against the property_id
      // but we want it saving against the version id
      //$table->set('_tbl_key', 'id');
      $table->set('_tbl_keys', array('id'));


      // Bind the data.
      if (!$table->bind($data))
      {
        $this->setError($table->getError());
        Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
      }

      // Prepare the row for saving
      $this->prepareTable($table);

      // Check the data. Use this to increment the counter for unit?
      if (!$table->check())
      {
        $this->setError($table->getError());
        Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
      }

      // Store the data.
      if (!$table->store())
      {
        $this->setError($table->getError());
        Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
      }

      // The version id is the id of the version created/updated in the _unit_versions table
      $new_version_id = ($table->id) ? $table->id : '';

      $this->setState('new.version.id', $new_version_id);

      // We will always want to update the facilities relating to the version id
      // E.g. if a new unit, insert facilitites, if new version then we will
      // save the facilities against the new version id.

      JLog::add('About to save facilities for unit version ID' . $new_version_id, 'DEBUG', 'unitversions');

      if (!$this->savePropertyFacilities($data, $table->unit_id, $old_version_id, $new_version_id))
      {
        Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
      }

      // When a new version is created or a new unit is created
      if ($this->new_version_required === true || $isNew)
      {

        // Here we have created a new version or a new unit
        // Update the existing property listing to indicate that it has been modified and that it 
        // requires a review
        $property = $this->getTable('Property', 'RentalTable');
        // Set the table properties
        $property->id = $table->property_id;
        $property->review = 1;
        $property->modified = JFactory::getDate();
        // Logger
        JLog::add('About to update Property review status for ' . $property->id, 'DEBUG', 'unitversions');
        // Attempt to save the new property details against the property id
        if (!$property->store())
        {
          $this->setError($property->getError());
          Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
        }

        // Actually, here we need to create a completely new version of this property
        // Otherwise, we might not have a previous version to compare to when generating payment etc.        
        //$property_version = $this->getTable('PropertyVersions', 'RentalTable');

        //if (!$property_version->load($table->property_id))
        //{
          //JLog::add('There was a problem loading the Property details for ' . $property_version->id . 'when trying to create new unit for ' . $table->unit_id, 'DEBUG', 'unitversions');
          //Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
        //}
        // Unset the version id, to ensure a new version is created.
        //unset($property_version->id);
        // Logger 
        //JLog::add('About to create new property version for ' . $property->id, 'DEBUG', 'unitversions');
        // Attempt to save the new property details against the property id
        //if (!$property_version->store())
        //{
          //$this->setError($property_version->getError());
          //Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
        //}

        // If this is not a new unit, then we want to copy the unit images to the new version...
        if (!$isNew)
        {

          // Copy the images against the new version id, but only if the versions are different
          // If we are updating a new unpublished version, no need to copy images
          if ($old_version_id != $new_version_id)
          {
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

      $this->setError($e->getMessage());

      // Log the exception
      JLog::add('There was a problem: ' . $e->getMessage(), 'DEBUG', 'unitversions');
      return false;
    }

    // Set the table key back to the parent id so it redirects based on that key
    // on not the version key id
    //$table->set('_tbl_key', 'unit_id');
    $table->set('_tbl_keys', array('unit_id'));


    $pkName = $table->getKeyName();

    if (isset($table->$pkName))
    {
      $this->setState($this->getName() . '.id', $table->$pkName);
    }
    $this->setState($this->getName() . '.new', $isNew);
    $this->setState($this->getName() . '.review', $table->review);
    $this->setState($this->getName() . '.version_id', $table->id);
    $this->setState($this->getName() . '.unit_id', $table->unit_id);


    return true;
  }

  public function copyUnitImages($old_version_id = '', $new_version_id = '')
  {

    // Get a list of all images stored against the old version
    $image = JModelLegacy::getInstance('Images', 'RentalModel', $config = array('ignore_request' => true));

    $image->setState('version_id', $old_version_id);

    // Get the images assigned to this old unit version id
    $images = $image->getItems();

    // If there are no images for the current version then return
    if (empty($images))
    {
      return true;
    }

    // Get a db instance
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->insert('#__property_images_library');

    $query->columns(array('version_id', 'unit_id', 'image_file_name', 'caption', 'ordering'));

    foreach ($images as $image)
    {
      // Only insert if there are some images
      $insert_string = "$new_version_id, '" . $image->unit_id . "','" . $image->image_file_name . "','" . mysql_real_escape_string($image->caption) . "','" . $image->ordering . "'";
      $query->values($insert_string);
    }

    // Execute the query
    $this->_db->setQuery($query);



    if (!$db->execute($query))
    {
      Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
    }
    return true;
  }

  /*
   * Method to save the property attributes into the #__attribute_property table.
   *
   *
   *
   */

  protected function savePropertyFacilities($data = array(), $id = 0, $old_version_id = '', $new_version_id = '')
  {

    if (!is_array($data) || empty($data))
    {
      return true;
    }

    if (empty($old_version_id) || empty($new_version_id))
    {
      return true;
    }

    $attributes = array();

    // For now whitelist the attributes that are supposed to be processed here...access options need adding.
    $whitelist = array('external_facilities', 'internal_facilities', 'kitchen_facilities', 'suitability');

    // Loop over the data and prepare an array to save
    foreach ($data as $key => $value)
    {

      if (!in_array($key, $whitelist))
      {
        continue;
      }

      // We're not interested in the 'other' fields E.g. external_facilities_other
      if (strpos($key, 'other') == 0 && !empty($value))
      {

        // Location, property and accommodation types are all single integers and not arrays
        if (is_array($value))
        {
          // We want to save this in one go so we make an array
          foreach ($value as $facility)
          {
            // Facilities should be integers
            if ((int) $facility)
            {
              $attributes[] = $facility;
            }
          }
        }
        else
        {
          $attributes[] = $value;
        }
      }
    }

    // If we have any attributes
    if (count($attributes) > 0)
    {

      // Get instance of the tariffs table
      $attributesTable = JTable::getInstance('PropertyAttributes', 'RentalTable', $config = array());

      // Bind the translated fields to the JTable instance
      if (!$attributesTable->save($id, $attributes, $old_version_id, $new_version_id))
      {
        JApplication::enqueueMessage(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_ADDING_ATTRIBUTES'), 'warning');

        JError::raiseWarning(500, $attributesTable->getError());
        return false;
      }

      return true;
    }

    return true;
  }

  /**
   * Method to create a 'unit' entry into the #__unit table.
   * This needs to be done prior to saving the version into #__unit_versions for new props
   * 
   * TO DO: This method isn't really needed. The unit model has a save method 
   * which could be as follows: 
   * 
   * $unit = JModelLegacy::GetInstance etc
   * $unit->save($data);
   * public function save($data){  
   * $return = true;
   * if (parent::save($data)){
   * $id =  (int) $this->getState($this->getName().'.id');
   * //Here you can do other tasks with your newly saved record...
   * } else {
   * $return = false;
   * }
   * return $commit;
   * }
   * @param type $data
   * @return mixed
   */
  public function createNewUnit($data = array())
  {

    if (empty($data))
    {
      return false;
    }

    $unit_table = $this->getTable('Unit', 'RentalTable');



    // Optional further sanity check after data has been validated, filtered, and about to be checked...
    //$this->prepareTable($property_table);
    if (!$unit_table->save($data))
    {
      return false;
    }

    return $unit_table->id;
  }

  /**
   * getImages = gets a list of images based on the version ID passed
   * @param type $version_id
   * @return type
   */
  public function getImages($version_id = '')
  {

    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Get a list of the images uploaded against this listing
    $query->select('
      id,
      unit_id,
      image_file_name,
      caption,
      ordering,
      version_id
    ');
    $query->from('#__property_images_library');

    $query->where('version_id = ' . (int) $version_id);

    $query->order('ordering', 'asc');

    $db->setQuery($query);

    $images = $db->loadAssocList();

    if (empty($images))
    {
      return false;
    }

    return $images;
  }

}
