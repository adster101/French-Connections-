<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewImages extends JViewLegacy
{
	/**
	 * display method of Availability View
	 * @return void
	 */
	public function display($tpl = null) 
	{
		// Get the property ID we are editing.
		$this->item->id = JRequest::getVar('id');
	   
    // Get the item data
    $item = $this->get('Item');

    // Assign the Item
		$this->item = $item;
    	 
		// Display the template
		parent::display($tpl);
 
	}
	
}
