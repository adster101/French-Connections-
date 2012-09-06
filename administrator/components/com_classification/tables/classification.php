<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.tablenested');

/**
 * Hello Table class
 */
class ClassificationTableClassification extends JTableNested
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__classifications', 'id', $db);
	}	
  
 	public function bind($array, $ignore = '') 
  {
    
		return parent::bind($array, $ignore);
    
  }
  
  public function store (  )
  {
    
    // Add more validation and what not here?
    
		$this->setLocation($this->parent_id, 'last-child');
		
		// Attempt to store the data.
		return parent::store();
    
  }
  
}
