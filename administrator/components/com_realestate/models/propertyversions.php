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
   * Take a copy of the images for this property.. 
   * 
   * @param type $old_version_id
   * @param type $new_version_id
   * @return boolean
   * @throws Exception 
   */
  public function copyUnitImages($old_version_id = '', $new_version_id = '')
  {

    // Get a list of all images stored against the old version
    $image = JModelLegacy::getInstance('Images', 'RealEstateModel', $config = array('ignore_request' => true));

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

    $query->insert('#__realestate_property_images_library');

    $query->columns(array('version_id', 'realestate_property_id', 'image_file_name', 'caption', 'ordering'));

    foreach ($images as $image)
    {
      // Only insert if there are some images
      $insert_string = "$new_version_id, '" . $image->realestate_property_id . "','" . $image->image_file_name . "','" . mysql_real_escape_string($image->caption) . "','" . $image->ordering . "'";
      $query->values($insert_string);
    }

    // Execute the query
    $this->_db->setQuery($query);



    if (!$db->execute($query))
    {
      Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_REALESTATE_PROPERTY_VERSION', $this->getError()));
    }
    return true;
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
  public function getTable($type = 'PropertyVersions', $prefix = 'RealEstateTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
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
    $data = JFactory::getApplication()->getUserState('com_realestate.edit.propertyversions.data', array());

    if (empty($data))
    {
      $data = $this->getItem();
    }

    return $data;
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


    $canDo = PropertyHelper::getActions();
    $this->setState('actions.permissions', $canDo);

    // List state information.
    parent::populateState();
  }

  /**
   * 
   * 
   */
  public function preprocessForm(\JForm $form, $data, $group = 'content')
  {

    $input = JFactory::getApplication()->input->get('jform', false, 'array');

    if (empty($input['use_invoice_details']) && ($input))
    {
      // User has selected not to use the invoice address. Therefore these fields are required.
      $form->setFieldAttribute('first_name', 'required', 'true');
      $form->setFieldAttribute('surname', 'required', 'true');
      $form->setFieldAttribute('phone_1', 'required', 'true');
      $form->setFieldAttribute('email_1', 'required', 'true');
    }
    
    parent::preprocessForm($form, $data);
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
    $table = $this->getTable();
    // Get an instance of the property model so we can load the property details
    $model = JModelLegacy::getInstance('Property', 'RealEstateModel', $config = array('ignore_request' => 'true'));
    $key = $table->getKeyName();
    $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
    $isNew = true;

    /*
     * We need to check if the use_invoice_details flag is set. If not present then need to update the field.
     */
    if (empty($data['use_invoice_details']))
    {
      $data['use_invoice_details'] = false;
    }

    // Allow an exception to be thrown.
    try {
      // Load the parent property details. 
      $property = $model->getItem($pk);

      if (!$property)
      {
        Throw New Exception(JText::_('COM_RENTAL_HELLOWORLD_PROBLEM_SAVING_PROPERTY', $this->getError()));
      }

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


      // Load the exisiting property version row
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
        if (!empty($property->expiry_date))
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

      // The version id is the id of the version created/updated in the _unit_versions table
      $new_version_id = ($table->id) ? $table->id : '';

      // If not a new and review state == 0 (e.g. an existing property version)  
      // $data['review'] - refers to the property version review state
      if (!$isNew)
      {
        // Copy the images against the new version id, but only if the versions are different
        // If we are updating a new unpublished version, no need to copy images
        if ($old_version_id != $new_version_id)
        {
          JLog::add('About to copy images for realestate property ' . $pk, 'debug', 'realestatepropertyversions');

          $this->copyUnitImages($old_version_id, $new_version_id);
        }

        // Update the review status, if it's not already been submitted.
        if ($property->review < 2)
        {
          // Update the existing property table review state to indicate that the listing has been updated
          $property->id = $table->realestate_property_id;

          $property->review = 1;

          $property_data = JArrayHelper::fromObject($property);

          if (!$model->save($property_data))
          {
            $this->setError($property->getError());
            return false;
          }
        }
      }

      $this->setState('new.version.id', $new_version_id);

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

}

