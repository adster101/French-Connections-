<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewLocate extends JViewLegacy
{
	/**
	 * display method of Availability View
	 * @return void
	 */
	public function display($tpl = null) 
	{
    
    
    // Get the property ID from the GET variable
    $this->property_id = JRequest::getVar( 'property_id', '', 'GET', 'int' );   
    
    // Get the image file ID of which we need to delete
    $this->file_id = JRequest::getVar ('id','','GET','int');
    
    // Get the caption details for the image
    $this->form = $this->get('Form');
    		
		// Display the template
		parent::display($tpl);
 
	}
	

}
