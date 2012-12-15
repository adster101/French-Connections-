<?php    

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
 
/**
 * HTML View class for the HelloWorld Component
 */
class ReviewsViewAdd extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
    
    $this->form = $this->get('Form');
    
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
		$document->setTitle(JText::_('') . ' - ' . JText::_('COM_HELLOWORLD_SITE_HOLIDAY_RENTAL_IN'));

  
    
	}	
}
