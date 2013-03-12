<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.table');

// import the model helper lib
//jimport('joomla.application.component.model');

/**
 * Hello Table class
 */
class HelloWorldTablePropertyUnits extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__property_units', 'id', $db);
	}	
	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 *
	 * @link    http://docs.joomla.org/JTable/load
	 * @since   11.1
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function progress($pk = '', $reset = true)
	{
		
		if ($reset)
		{
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('
      (select count(*) from qitz3_attributes_property where property_id = ' . (int) $pk . ') as facilities,
      (select count(*) from qitz3_availability where id = ' . (int) $pk . ' and start_date > CURDATE()) as availability,
      (select count(*) from qitz3_tariffs where id =  ' . (int) $pk . ' and start_date > CURDATE()) as tariffs,
      (select count(*) from qitz3_images_property_gallery where property_id =  ' . (int) $pk . ') as images
    ');

   
		$this->_db->setQuery($query);

		$row = $this->_db->loadAssoc();

    // Check that we have a result.
		if (empty($row))
		{
			return false;
		}

		// Bind the object with the row and return.
		return $row;
	}
  
  /**
	 * Overloaded load function. This load the units for the given property ID.
	 *
	 * @param       int $id property id, not primary key in this case
	 * @param       boolean $reset reset data
	 * @return      boolean
	 * @see JTable:load
	 */
	public function load_units($id = null, $unit_id = null, $reset = true) 
	{
		$query = $this->_db->getQuery(true);
		$query->select('
      id, 
      parent_id,
      unit_title
    ');
		$query->from($this->_db->quoteName('#__property_units'));
		$query->where($this->_db->quoteName('parent_id') . ' = ' . $this->_db->quote($id));
    $query->order('ordering');
		$this->_db->setQuery($query);

    try
		{
			$result = $this->_db->loadObjectList();
			return $result;
		}
		catch (RuntimeException $e)
		{
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}			
	}  
  
	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 *
	 * @link    http://docs.joomla.org/JTable/load
	 * @since   11.1
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function load($id = '', $reset = true)
	{

		if ($reset)
		{
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('
      pl.title,
      pl.latitude,
      pl.longitude,
      pl.department,
      pu.*
    ');
    
		$query->from('#__property_units pu');
    
    $query->join('left', '#__property_listings pl on pl.id = pu.parent_id');

    // Add the search tuple to the query.
		$query->where('pu.id = ' . (int) $id);
		
		$this->_db->setQuery($query);

		$row = $this->_db->loadObject();
    
		// Check that we have a result.
		if (empty($row))
		{
			return false;
		}

		// Bind the object with the row and return.
		return $row;
	}
  
}
