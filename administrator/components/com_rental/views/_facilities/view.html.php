<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewFacilities extends JViewLegacy
{
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null) 
	{
		// Get the property ID we are editing.
		$this->item->id = JRequest::getVar('id');
    
    // Get the property title which should be stored in the user session
    $this->item->title = JApplication::getUserState('title'.$this->item->id, '');
    
		// Get the form
		$form = $this->get('Form');
    
  	// Assign the Data
		$this->form = $form;  
		    
    $languages = RentalHelper::getLanguages();
		$lang = RentalHelper::getLang();
		
    // Should this be done with views? 
		$view = strtolower(JRequest::getVar('view'));
    
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
    
		$this->languages = $languages;
		$this->lang = $lang;
		
		// Set the toolbar
		$this->addToolBar();
    
		// Display the template
		parent::display($tpl);
 
		// Set the document
		$this->setDocument();
	}
	
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		// Determine the layout we are using. 
		// Should this be done with views? 
		$view = strtolower(JRequest::getVar('view'));
    
    // Get the published state from the form data  
    $published = $this->form->getValue('published');

    
		// Add the tabbed submenu for the property edit view.
		RentalHelper::addSubmenu($view, $published);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;
		$canDo = RentalHelper::getActions($this->item->id);
		JToolBarHelper::title($isNew ? JText::_('COM_RENTAL_MANAGER_HELLOWORLD_NEW') : JText::sprintf('COM_RENTAL_FACILITIES_EDIT', $this->item->title), 'helloworld');
		
    // Built the actions for new and existing records.
		JToolBarHelper::apply('facilities.apply', 'JTOOLBAR_APPLY');	
    // Built the actions for new and existing records.
		JToolBarHelper::save('facilities.save', 'JTOOLBAR_SAVE');	
    // Cancel out to the helloworld(s) default view rather than the availabilities view...??
		JToolBarHelper::cancel('helloworld.cancel', 'JTOOLBAR_CANCEL');
    
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_RENTAL_HELLOWORLD_CREATING') : JText::_('COM_RENTAL_HELLOWORLD_EDITING_FACILITIES'));
		$document->addScript(JURI::root() . "/administrator/components/com_rental/js/submitbutton.js");

		$document->addStyleSheet(JURI::root() . "administrator/components/com_rental/css/helloworld.css",'text/css',"screen");

		JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
	}
}
