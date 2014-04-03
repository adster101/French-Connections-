<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewDeleteImage extends JViewLegacy
{
	/**
	 * display method of Availability View
	 * @return void
	 */
	public function display($tpl = null) 
	{
    


    // Get the parent property ID from the GET variable
    $parent_id = JRequest::getVar( 'parent_id', 1, 'GET', 'int');
    
    if ($parent_id != 1 && $parent_id > 0) {
      $this->id = (int) $parent_id;
    } else {
      // Get the property ID from the GET variable
     $this->id = JRequest::getVar( 'property_id', '', 'GET', 'int' );   
    }
    // Get the image file ID of which we need to delete
    $this->file_id = JRequest::getVar ('id','','GET','int');
		
		// Display the template
		parent::display($tpl);
 
		// Set the document
		//$this->setDocument();
	}
	

}
