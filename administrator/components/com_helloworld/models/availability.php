<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class HelloWorldModelAvailability extends JModelAdmin
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
	public function getTable($type = 'PropertyUnits', $prefix = 'HelloWorldTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Returns a the availability for this property
	 *
	 */
	public function getAvailability( $id = '' ) 
	{
    
    $id = (!empty($id)) ? $id : (int) $this->getState($this->getName() . '.id');
    
    $query = $this->getAvailabilityQuery($id);
    
    try
		{
      $this->_db->setQuery($query);
      $result = $this->_db->loadObjectList();		
    }
    
		catch (RuntimeException $e)
		
    {
			$this->setError($e->getMessage());
			return false;
		}
    
    
    return $result;
		
	}
	
  
	/**
	 * Method to generate a query to get the availability for a particular property
   * 
   * TO DO: Add a check to ensure that the user requesting the availability
   * is the owner... 
	 *
	 * @param       int $id property id, not primary key in this case
	 * @param       boolean $reset reset data
	 * @return      boolean
	 * @see JTable:load
	 */
	public function getAvailabilityQuery($id = null, $reset = true) 
	{
    // Create a new query object.		
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    
		$query = $this->_db->getQuery(true);
		$query->select('id, start_date, end_date, availability');
		$query->from($this->_db->quoteName('#__availability'));
		$query->where($this->_db->quoteName('id') . ' = ' . $this->_db->quote($id));
		$this->_db->setQuery($query);
		
    return $query;
    
	}  
	/**
	 * Method to get the record form. 
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	2.5
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_helloworld.helloworld', 'availability',
		                        array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_helloworld.edit.availability.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}	
	
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'administrator/components/com_helloworld/models/forms/availability.js';
	}
}
