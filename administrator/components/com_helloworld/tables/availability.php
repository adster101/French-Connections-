<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.tablenested');
 
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
	 * Overloaded bind function
	 *
	 * @param       array           named array
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = '') 
	{		
		if (isset($array['params']) && is_array($array['params']) && $this->params) 
		{
			// $this is an instance of HelloWorldTableHelloWorld (and includes a copy of the record as it stands in the db)
			// Loop over the $array['params']
			// For each check that this key isn't already stored as attribute
			// If it is delete it as there is a new one incoming
			// merge the two sets of data
			// so that both sets of params are preserved
			foreach($array['params'] as $key=>$value) {
				if ($this->params->getValue($key) !== null) {
					$tmp = $this->params->set($key, '');
				}
			}			
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$parameter->merge($this->params);
			$array['params'] = (string)$parameter;
		}
		return parent::bind($array, $ignore);
	}
 
	/**
	 * Overloaded load function
	 *
	 * @param       int $pk primary key
	 * @param       boolean $reset reset data
	 * @return      boolean
	 * @see JTable:load
	 */
	public function load($pk = null, $reset = true) 
	{
		if (parent::load($pk, $reset)) 
		{

			// Convert the params field to a registry.
			//$params = new JRegistry;
			//$params->loadJSON($this->params);
			//$this->params = $params;
			return true;
		}
		else
		{
			return false;
		}
	}
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_helloworld.message.'.(int) $this->$k;
	}
 
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetTitle()
	{
		return $this->greeting;
	}
 
	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 * @since	1.6
	 */
	protected function _getAssetParentId()
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_helloworld');
		return $asset->id;
	}

	/**
	 * Stores a property
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{
		// Attempt to store the data.
		return parent::store($updateNulls);
	}
}
