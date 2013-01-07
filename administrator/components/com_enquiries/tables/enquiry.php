<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.table');

// import the model helper lib
jimport('joomla.application.component.model');

/**
 * Hello Table class
 */
class EnquiriesTableEnquiry extends JTable
{
  
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__enquiries', 'id', $db);
	}
      
  public function load($pk = null, $reset = true) 
	{		
		if (parent::load($pk, $reset)) 
		{    
          
			return true;
		}
		else
		{
			return false;
		}
	}
}
