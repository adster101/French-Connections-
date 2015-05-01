<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class RentalModelUnit extends JModelAdmin
{

  /**
   * Method to test whether a user can edit the published state of a property.
   * This is overriden to check the unit rental.unit.reorder permission which is only necessary because
   * canEditState by default checks the core.edit.state permission which we have to deny from owners. 
   * 
   * @param   object  $record  A record object.
   *
   * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
   *
   * @since   11.1
   */
  protected function canEditState($table)
  {

    $comtask = JRequest::getVar('task', '', 'POST', 'string');
    $task = explode('.', $comtask);
    $user = JFactory::getUser();



    if ($task[1] == 'orderdown' || $task[1] == 'orderup')
    {
      return $user->authorise('rental.unit.reorder', $this->option);
    }
    // Uh oh, someone trying to unpublish a unit that is in the first position...
    elseif ($task[1] == 'unpublish' && $table->ordering == 1)
    {
      throw new Exception(JText::_('COM_RENTAL_CANNOT_UNPUBLISH_THIS_UNIT'));
    }
    elseif ($task[1] == 'trash' && $table->ordering == 1)
    {
      throw new Exception(JText::_('COM_RENTAL_CANNOT_UNPUBLISH_THIS_UNIT'));
    }
    else
    {
      return $user->authorise('core.edit.state', $this->option);
    }
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
  public function getTable($type = 'Unit', $prefix = 'RentalTable', $config = array())
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
    $form = $this->loadForm('com_rental.unit', 'unit', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  /**
   * A protected method to get a set of ordering conditions.
   *
   * @param   object	A record object.
   *
   * @return  array  An array of conditions to add to add to ordering queries.
   * @since   1.6
   */
  protected function getReorderConditions($table)
  {
    $condition = array();
    $condition[] = 'property_id = ' . (int) $table->property_id;
    return $condition;
  }

}
