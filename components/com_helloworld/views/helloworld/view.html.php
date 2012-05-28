<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class HelloWorldViewHelloWorld extends JView
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		// Assign data to the view
		$this->item = $this->get('Item');
 
 		$script = $this->get('Script');

 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Display the view
		parent::display($tpl);
		// Set the document
		$this->setDocument();		
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_($this->item->greeting).' - '.JText::_('COM_HELLOWORLD_SITE_HOLIDAY_RENTAL_IN').$this->item->nearest_town);
		$document->addScript("https://maps.googleapis.com/maps/api/js?key=AIzaSyBYcwtxu1C9l9O3Th0W6W_X4UtJi9zh2i8&sensor=true");
	}	
}
