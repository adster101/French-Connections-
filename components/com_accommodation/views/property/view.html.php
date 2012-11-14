<?php    

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
 
/**
 * HTML View class for the HelloWorld Component
 */
class AccommodationViewProperty extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		// Assign data to the view
		$this->item = $this->get('Item');
    
    //$this->facilities = $this->get('Facilities');
    
    

    
    $this->availability = $this->get('Availability');

    
    
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
    
		// Display the view
		parent::display($tpl)
            ;
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
		$document->setTitle(JText::_($this->item->title) . ' - ' . JText::_('COM_HELLOWORLD_SITE_HOLIDAY_RENTAL_IN') . $this->item->nearest_town);
		$document->addScript("https://maps.googleapis.com/maps/api/js?key=AIzaSyBYcwtxu1C9l9O3Th0W6W_X4UtJi9zh2i8&sensor=true");
		$document->addScript("http://s7.addthis.com/js/250/addthis_widget.js#pubid=frenchconnections",'text/javascript', true, true);
    $document->addStyleSheet(JURI::root() . "/components/com_accommodation/css/styles.css",'text/css',"screen");
    $document->addStyleSheet(JURI::root() . "/administrator/components/com_helloworld/css/availability.css",'text/css',"screen");
  
    
	}	
}
