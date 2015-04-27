<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class RentalModelCaption extends JModelAdmin
{

  /**
   * Method override to check if you can edit an existing record.
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   * @since	1.6
   */
  protected function allowEdit($data = array(), $key = 'id')
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
  public function getTable($type = 'Image', $prefix = 'RentalTable', $config = array())
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
  public function getForm($data = array(), $loadData = false)
  {

    // Get the form.
    $form = $this->loadForm('com_rental.caption', 'caption', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  /**
   * Save method to save an newly upload image file, taking into account a new version if necessary.
   * 
   * @param type $data
   */
  public function save($data)
  {
    $caption_id = $data['id'];
    $model = JModelLegacy::getInstance('UnitVersions', 'RentalModel');

    // Need to look up the unit review status here to ensure that we upload new images against the correct version
    $unit = $model->getItem($data['unit_id']);

    // Set the review state to that of the latest unit version which will have previously been updated (if this is a 2nd, 3rd or 4th image upload etc
    $data['review'] = $unit->review;
    $data['id'] = $unit->id;
    $data['property_id'] = $unit->property_id;

    // Hit up the unit versions save method to determine if a new version is needed.
    if (!$model->save($data))
    {
      return false;
    }

    $table = $this->getTable();

    // Here we check if a new version has been created...
    if ($model->new_version_required)
    {
      // Look up the old image data as a new unit version has been created
      // along with a new set of images saved against the new version id (which means we have to 
      // update the new caption against a different image object...
      $image = $this->getItem($caption_id);
      unset($data['id']);
      $data['image_file_name'] = $image->image_file_name;
      $data['ordering'] = $image->ordering;
      $data['version_id'] = $model->getState($model->getName() . '.version_id');
      $table->set('_tbl_keys', array('image_file_name','version_id'));
    }
    else
    {
      $data['id'] = $caption_id;
      $data['version_id'] = $model->getState($model->getName() . '.version_id');
    }

    // Arrange the data for saving into the images table

    // Call the parent save method to save the actual image data to the images table
    $key = $table->getKeyName();

    // Allow an exception to be thrown.
    try
    {

      // Bind the data.
      if (!$table->bind($data))
      {
        $this->setError($table->getError());

        return false;
      }

      // Prepare the row for saving
      $this->prepareTable($table);

      // Store the data.
      if (!$table->store())
      {
        $this->setError($table->getError());
        return false;
      }

      // Clean the cache.
      $this->cleanCache();
    }
    catch (Exception $e)
    {
      $this->setError($e->getMessage());

      return false;
    }

    if (isset($table->$key))
    {
      $this->setState($this->getName() . '.id', $table->$key);
    }

    return true;
  }

}
