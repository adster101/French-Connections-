<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class RentalModelPropertyVersions extends JModelAdmin
{

  public function copyPropertyAttributes($old_version_id = '', $new_version_id = '')
  {
    $db = JFactory::getDBO();

    $query = $db->getQuery(true);

    $query->select('version_id, property_id, attribute_id')
            ->from('#__property_attributes')
            ->where('version_id=' . (int) $old_version_id);

    $db->setQuery($query);

    $facilities = $db->loadRowList();

    if (!$facilities)
    {
      return true;
    }

    // Clear the query object so we can reuse it
    $query->clear();

    $query->insert('#__property_attributes');
    $query->columns(array('version_id', 'property_id', 'attribute_id'));

    foreach ($facilities as $facility)
    {
      // Replace the old version ID with the new one...
      $facility[0] = $new_version_id;

      $query->values(implode(',', $facility));
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
   *
   * Method to create a 'parent' entry into the #__property table.
   * This needs to be done prior to saving the version into #__property_versions for new props
   *
   */

  public function createNewProperty($data = array())
  {

    if (empty($data))
    {
      return false;
    }

    // Set the review status to 1
    $data['review'] = 1;

    $property_table = $this->getTable('Property', 'RentalTable');

    if (!$property_table->bind($data))
    {
      $this->setErrro($property_table->getError());
      return false;
    }

    // Optional further sanity check after data has been validated, filtered, and about to be checked...
    //$this->prepareTable($property_table);
    if (!$property_table->store())
    {
      return false;
    }

    return $property_table->id;
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
    $form = $this->loadForm('com_rental.propertyversions', 'propertyversions', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
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
  protected function loadFormData()
  {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_rental.edit.propertyversions.data', array());

    if (empty($data))
    {
      $data = $this->getItem();
    }

    return $data;
  }

  public function getItem($pk = null)
  {

    if ($item = parent::getItem($pk))
    {

      $registry = new JRegistry;
      $registry->loadString($item->local_amenities);
      $item->amenities = $registry->toArray();

      // Use the primary key (in this case unit id) to pull out any existing tariffs for this property
      $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

      $attributes = $this->getFacilities($pk, $item->id);

      foreach ($attributes as $key => $values)
      {
        $item->$key = $values;
      }
    }

    /*
     * Explode the local_amenities to individual fields so we can use then in the location view.
     * 
     */

    return $item;
  }

  /**
   * Get the facilities for this property
   * 
   */
  public function getFacilities($id, $version, $table = '#__property_attributes b')
  {

    // Array to hold the result list
    $property_attributes = array();

    $properties = array();

    // Loads a list of the attributes that we are interested in
    // This is probably reused on the search part
    $query = $this->_db->getQuery(true);

    $query->select('d.field_name, b.attribute_id');
    $query->from($table);
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

  /*
   * param JForm $form The JForm instance for the view being edited
   * param array $data The form data as derived from the view (may be empty)
   *
   * @return void
   *
   */

  protected function preprocessForm(JForm $form, $data)
  {

    // Convert data to object if it's an array
    if (is_array($data))
    {
      $data = JArrayHelper::toObject($data, 'JObject');
    }

    // Set the location details accordingly, needed for one of the form field types...
    if (!empty($data->department))
    {

      $form->setFieldAttribute('city', 'department', $data->department);
      $form->setFieldAttribute('city', 'readonly', 'false');
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
  protected function getLocationDetails($city)
  {

    $location_details_array = array();

    // Get the table instance for the classification table
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');

    $table = $this->getTable('Classification', 'ClassificationTable');

    if (!$location_details = $table->getPath($city))
    {
      $this->setError($table->getError());
      return false;
    };

    // Loop over the location details and pass them back as an array
    foreach ($location_details as $key => $value)
    {

      if ($value->level > 0)
      {
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

    $canDo = RentalHelper::getActions();
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
  public function save($data)
  {

    $dispatcher = JEventDispatcher::getInstance();
    $table = $this->getTable();
    $model = JModelLegacy::getInstance('Property', 'RentalModel', $config = array('ignore_request' => 'true'));
    $key = $table->getKeyName();
    $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
    $isNew = true;

    // Get an db instance and start a transaction
    $db = JFactory::getDBO();
    $db->transactionStart();

    $city = (!empty($data['city'])) ? $data['city'] : '';

    // Get the location details (area, region, dept) and update the data array
    $location_details = $this->getLocationDetails($city);

    // Update the location details in the data array...ensures that property will always be in the correct area, region, dept, city etc
    if (!empty($location_details))
    {
      $data['country'] = $location_details[0];
      $data['area'] = $location_details[1];
      $data['region'] = $location_details[2];
      $data['department'] = $location_details[3];
      $data['city'] = $location_details[4];
    }

    // Wrap up the amenities if they are present and save 'em
    if (isset($data['amenities']) && is_array($data['amenities']))
    {
      $registry = new JRegistry;
      $registry->loadArray($data['amenities']);
      $data['local_amenities'] = (string) $registry;
    }

    // Allow an exception to be thrown.
    try
    {
      // Load the exisiting row, if there is one.
      if ($pk > 0)
      {
        $table->load($pk);
        $isNew = false;
      }

      // Get the version id of the property being editied.
      $old_version_id = ($table->id) ? $table->id : '';

      // If this is a new propery then we need to generate a 'stub' entry into the propery table
      // which essentially handles the non versionable stuff (like expiry data, ordering and published state).

      if ($isNew)
      {

        $new_property_id = $this->createNewProperty($data);

        if (!$new_property_id)
        {

          // Problem creating the new property stub...
          $this->setError('There was a problem creating your property. Please try again.');
          return false;
        }

        $data['property_id'] = $new_property_id;
        $data['review'] = 1;
      }

      // If $data['review'] is not set we need to check whether a new version is required
      if ($data['review'] == 0)
      {

        // Need to verify the expiry date for this property. If no expiry date then no new version is required.
        // New method - getExpiryDate(); returns the expiry date of the property.
        $expiry_date = $model->getPropertyDetail($data['property_id']);

        if (is_integer($expiry_date))
        {
          // As a new version is required amend the data array before we save
          // id here refers to the version id. Unsetting this effectively forces the table class to 
          // create a new entry rather than updating an existing row.
          $data['id'] = '';
          $data['review'] = '1';
          $data['published_on'] = '';
        }
      }

      // Set the table model to the appropriate key
      // If we don't do this, the model will save against the property_id
      // but we want it saving against the version id
      $table->set('_tbl_keys', array('id'));

      // Store the data, regardless of the review state
      if (!$table->save($data))
      {
        $this->setError($table->getError());
        return false;
      }

      // If not a new and review state == 0 (e.g. an existing property version)  
      // $data['review'] - refers to the property version review state
      if (!$isNew)
      {

        // We need to check the review state of the property in case it's a PFR (review state 2) 
        $property = $this->getTable('Property', 'RentalTable');

        // Load the parent property details. TO DO should probably reuse getPropertyDetail method and cache
        if (!$property->load($table->property_id))
        {
          Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_PROPERTY', $this->getError()));
        }

        // Update the review status, if it's not already been submitted.
        if ($property->review < 2)
        {
          // Update the existing property table review state to indicate that the listing has been updated
          $property->id = $table->property_id;

          $property->review = 1;

          if (!$property->store())
          {
            $this->setError($property->getError());
            return false;
          }
        }
      }

      // The version id is the id of the version created/updated in the _unit_versions table
      $new_version_id = ($table->id) ? $table->id : '';

      $this->setState('new.version.id', $new_version_id);

      // We will always want to update the facilities relating to the version id
      // E.g. if a new unit, insert facilitites, if new version then we will
      // save the facilities against the new version id.

      JLog::add('About to save facilities for property (' . $table->property_id . 'version ID' . $new_version_id, 'DEBUG', 'unitversions');

      if (!$this->savePropertyFacilities($data, $table->property_id, $old_version_id, $new_version_id, $isNew))
      {
        Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_UNIT', $this->getError()));
      }

      // Commit the transaction
      $db->transactionCommit();

      // Clean the cache.
      $this->cleanCache();
    }
    catch (Exception $e)
    {

      // Roll back any queries executed so far
      $db->transactionRollback();

      $this->setError($e->getMessage());

      // Log the exception
      JLog::add('There was a problem: ' . $e->getMessage(), JLog::ALL, 'propertyversions');
      return false;
    }

    // Set the table key back to the parent id so it redirects based on that key
    // on not the version key id
    //$table->set('_tbl_key', 'property_id');
    $table->set('_tbl_keys', array('property_id'));

    $pkName = $table->getKeyName();

    if (isset($table->$pkName))
    {
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

  protected function savePropertyFacilities($data = array(), $id = 0, $old_version_id = '', $new_version_id = '', $isNew = false)
  {
    // TO DO - Surely there is a better way to do this?
    $attributes = array();

    // For now whitelist the attributes that are supposed to be processed here...
    $whitelist = array('activities', 'access');

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
          foreach ($value as $attribute)
          {
            // Facilities should be integers
            if ((int) $attribute)
            {
              $attributes[] = $attribute;
            }
          }
        }
        else
        {
          $attributes[] = $value;
        }
      }
    }

    // No attributes means that we're saving from the enquiry settings screen...
    if (empty($attributes))
    {
      // We're saving a unit either from the images or tariffs screen. 
      // If this is a new version for an existing unit create a copy of the
      // facilities against the new version 
      if (($old_version_id != $new_version_id) && !$isNew)
      {
        $this->copyPropertyAttributes($old_version_id, $new_version_id);
      }

      return true;
    }
    else if (count($attributes) > 0)
    {

      // Firstly need to delete these...in a transaction would be better
      $query = $this->_db->getQuery(true);

      if ($old_version_id == $new_version_id)
      {

        $query->delete('#__property_attributes')
                ->where('version_id = ' . $old_version_id);
        $this->_db->setQuery($query);

        if (!$this->_db->execute())
        {

          $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));

          $this->setError($e);
          return false;
        }
      }



      $query = $this->_db->getQuery(true);

      $query->insert('#__property_attributes');

      $query->columns(array('version_id', 'property_id', 'attribute_id'));

      foreach ($attributes as $attribute)
      {
        $insert_string = "$new_version_id, $id," . $attribute . "";
        $query->values($insert_string);
      }

      $this->_db->setQuery($query);

      if (!$this->_db->execute())
      {
        $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));
        $this->setError($e);
        return false;
      }


      return true;
    }


    return true;
  }

}

