<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Classification View
 */
class AttributesViewAttribute extends JViewLegacy
{
	/**
	 * display method of Attribute view
	 * @return void
	 */
	public function display($tpl = null) 
	{

		// get the Data
		$item = $this->get('Item');
    
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Assign the Data
		$this->item = $item;

    // Set the toolbar
		//$this->addToolBar();
 
		// Display the template
		parent::display($tpl);
    
    // Set the document
		//$this->setDocument();
    
	}
	

}
