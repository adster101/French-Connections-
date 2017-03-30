<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * Submit model
 *
 * These two functions should be moved to the listing model for consistency
 *
 */
class RentalModelSubmit extends JModelAdmin
{

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
    $form = $this->loadForm('com_rental.submit', 'submit', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return  array    The default data is an empty array.
   *
   * @since   12.2
   */
  protected function loadFormData()
  {


    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_rental.view.listing.data', array());

    if (empty($data))
    {

      // Load the VAT status details from the user profile table...
      // Here we simply bind the propery ID being edited to the form.
      $input = JFactory::getApplication()->input;

      $id = $input->get('id', '', 'int');
    }

    return $data;
  }

  /**
   * The submisssion notes are saved...into the notes table
   * TO DO - Change this to use the notes model so it can be reused
   * 
   * @param type $data
   */
  public function save($data)
  {

    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_notes/models');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_notes/tables');
    
    $note = JModelLegacy::getInstance('Note', 'NotesModel');
    
    
    
    if (!$note->save($data))
    {
      // TO DO Log this
      return false;
    }   
    
    return true;
  }

}