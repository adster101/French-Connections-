<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.table');

/**
 * Hello Table class
 */
class HelloWorldTableTariffs extends JTable
{
  
  
  
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__tariffs', 'id', $db);
	}
}
