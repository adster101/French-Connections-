<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.table');
 
/**
 * Hello Table class
 */
class HelloWorldTableAvailability extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__availability', 'id', $db);
	}
	
	/**
	 * Overloaded load function
	 *
	 * @param       int $id property id, not primary key in this case
	 * @param       boolean $reset reset data
	 * @return      boolean
	 * @see JTable:load
	 */
	public function load($id = null, $reset = true) 
	{
		$query = $this->_db->getQuery(true);
		$query->select('id, start_date, end_date, status');
		$query->from($this->_tbl);
		$query->where($this->_db->quoteName('id') . ' = ' . $this->_db->quote($id));
	
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
}
