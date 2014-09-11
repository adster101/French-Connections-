<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('frenchconnections.models.property.propertyversions');

/**
 * HelloWorld Model
 */
class RealEstateModelPropertyVersions extends PropertyModelVersions
{

  
  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'PropertyVersions', $prefix = 'RealEstateTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
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

    $canDo = RealEstateHelper::getActions();
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
  public function save($data = array())
  {

    $dispatcher = JEventDispatcher::getInstance();
    $table = $this->getTable();
    $model = JModelLegacy::getInstance('Property', 'RealEstateModel', $config = array('ignore_request' => 'true'));
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

    // Allow an exception to be thrown.
    try {
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
        $new_property_id = $this->createNewProperty('Property', 'RealEstateTable');

        if (!$new_property_id)
        {
          // Problem creating the new property stub...
          $this->setError('There was a problem creating your property. Please try again.');
          return false;
        }

        $data['realestate_property_id'] = $new_property_id;
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
      if (!$isNew && $data['review'] == 0)
      {

        // We need to check the review state of the property in case it's a PFR (review state 2) 
        $property = $this->getTable('Property', 'RealEstateTable');

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

      JLog::add('About to save facilities for property (' . $table->realestate_property_id . 'version ID' . $new_version_id, 'DEBUG', 'unitversions');

 
      // Commit the transaction
      $db->transactionCommit();

      // Clean the cache.
      $this->cleanCache();
    }
    catch (Exception $e) {

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
    $table->set('_tbl_keys', array('realestate_property_id'));

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

  protected function savePropertyFacilities($data = array(), $id = 0, $old_version_id = '', $new_version_id = '', $table = '', $attribute_liat = array())
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

    // Loop over the data and prepare an array to save
    foreach ($data as $key => $value)
    {

      if (!in_array($key, $attribute_liat))
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

      // Firstly need to delete these...in a transaction would be better
      $query = $this->_db->getQuery(true);

      if ($old_version_id == $new_version_id)
      {

        $query->delete($table)->where('version_id = ' . $old_version_id);
        $this->_db->setQuery($query);

        if (!$this->_db->execute())
        {

          $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));

          $this->setError($e);
          return false;
        }
      }



      $query = $this->_db->getQuery(true);

      $query->insert($table);

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

