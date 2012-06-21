<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewAvailability extends JView
{
	/**
	 * display method of Availability View
	 * @return void
	 */
	public function display($tpl = null) 
	{
		// Get the property ID we are editing.
		$this->item->id = JRequest::getVar('id');
		// Import the form class
		jimport('joomla.application.component.modelform');
		$models_path = JPATH_COMPONENT . DS . 'models';
		// Set the form and field paths
		JForm::addFormPath($models_path . DS . 'forms');
		JForm::addFieldPath($models_path . DS . 'fields');
		
		// Get the form statically, for now not trying to load any form data as it's not needed here
		$form = JForm::getInstance('com_helloworld.helloworld','availability', array('load_data' => 'false'));
		
		// get an instance of the availability table
		$table = $this->get('Table');
		
		// Get the actual availability for this property 
		$this->availability = $table->load($this->item->id);	
		
		// Build the calendar taking into account current availability...
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Assign the Data
		$this->form = $form;
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
		// Add the tabbed submenu for the property edit view.
		HelloWorldHelper::addSubmenu($view);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;
		$canDo = HelloWorldHelper::getActions($this->item->id);
		JToolBarHelper::title($isNew ? JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_NEW') : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'), 'availability');
		// Built the actions for new and existing records.
		
		JToolBarHelper::apply('availability.apply', 'JTOOLBAR_APPLY');	
		JToolBarHelper::cancel('availability.cancel', 'JTOOLBAR_CANCEL');
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
		$document->setTitle($isNew ? JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING') : JText::_('COM_HELLOWORLD_HELLOWORLD_EDITING'));
		//$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_helloworld/views/helloworld/submitbutton.js");
		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
	}
}
