<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorldList Model
 */
class RealestateModelProperty extends JModelAdmin
{

  public function getSubmitForm($data = array(), $loadData = true)
  {
    JForm::addFormPath(JPATH_LIBRARIES . '/frenchconnections/forms');
    $form = $this->loadForm('com_realestate.submit', 'submit', array('control' => 'jform', 'load_data' => $loadData));

    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  public function getForm($data = array(), $loadData = true)
  {
    JForm::addFormPath(JPATH_LIBRARIES . '/frenchconnections/forms');
    $form = $this->loadForm('com_realestate.listing', 'admin', array('control' => 'jform', 'load_data' => $loadData));

    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  public function loadFormData()
  {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_rental.view.listing.data', array());

    if (empty($data))
    {
      $data = $this->getItem();
    }

    return $data;
  }

  public function getTable($name = 'Property', $prefix = 'RealEstateTable')
  {
    return JTable::getInstance($name, $prefix);
  }

  /**
   * Returns the expiry date of a property item.
   * @param type $property_id
   * @return type
   */
  public function getPropertyDetail($property_id = '')
  {

    if (!$property_id)
    {
      return false;
    }

    // Get the table instance
    $property = $this->getTable('Property', 'RealestateTable');

    // And then set the property ID 
    $property->id = $property_id;

    // Load the data up
    if (!$property->load())
    {
      return false;
    }

    if (!empty($property->expiry_date))
    {
      $date = new JDate($property->expiry_date);
      $expiry_date = $date->toUnix();
    }
    else
    {
      $expiry_date = false;
    }


    return $expiry_date;
  }

  /*
   * Method to save the snooze until date against the propery, if set
   *
   */

  public function save($data)
  {
    // Check whether any data has been entered (e.g. snooze date, owner id or expiry date) 
    // Reformat the dates to the correct format
    // Call the parent save method.
    // Possible to a 'post save' hook...

    if (!empty($data['snooze_until']))
    {
      $data['snooze_until'] = JFactory::getDate($data['snooze_until'])->calendar('Y-m-d');
    }

    if (!empty($data['expiry_date']))
    {
      $data['expiry_date'] = JFactory::getDate($data['expiry_date'])->calendar('Y-m-d');
    }
    else
    {
      unset($data['expiry_date']);
    }

    // Unset the review status if it's not been updated.
    if (empty($data['review']))
    {
      unset($data['review']);
    }
    else
    {
      // Need to get the value of the renewal/update
    }
    
    if (empty($data['website_visible']))
    {
      $data['website_visible'] = '';
    }

    // Save the data
    if (!parent::save($data))
    {
      // Oops...
      return false;
    }

    if (!empty($data['subject']) || !empty($data['body']))
    {

      // Update the notes 
      // Get the note model instance
      JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_notes/models');
      JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_notes/tables');
      $note = JModelLegacy::getInstance('Note', 'NotesModel', $config = array('ignore_request' => true));

      // Set the property ID
      $data['property_id'] = $data['id'];

      unset($data['id']);
      
      // Save the mother load
      if (!$note->save($data))
      {
        return false;
      }
    }
    // Return back to the controller.
    return true;
  }

}

