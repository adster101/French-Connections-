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
class ReviewTableReview extends JTable
{
  
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__reviews', 'id', $db);
	}
      
  public function load($pk = null, $reset = true) 
	{		
		if (parent::load($pk, $reset)) 
		{    
      // Set the title here so we can see what attribute we are editing
      JToolBarHelper::title(JText::_('Manage review ('. $this->id.')'));
     
			return true;
		}
		else
		{
			return false;
		}
	}
}
