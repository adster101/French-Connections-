<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class PropertyModelVersions extends JModelAdmin
{

  /**
   * Method to create an entry into the table specified in the $type and $prefix args using table defaults.
   * This needs to be done prior to saving the version into the relevant version tables for new props
   * rental and realestate
   * 
   * @param type $type
   * @param type $prefix
   * @return boolean 
   */
  public function createNewProperty($type = '', $prefix = '')
  {

    $property_table = $this->getTable($type, $prefix);

    $data = $property_table->getProperties();

    // Set the review status to 1
    $data['review'] = 1;

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



}

