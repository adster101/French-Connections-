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
	
		// Get the custom script path for this screen
		$script = $this->get('Script');

		// Get the availability form, for now not loading any form data as it will be presented in the calendar rather than in a form
		// Need to take into account the additional price notes 
		$form = $this->get('Form');

		// get an instance of the availability table
		$table = $this->get('AvailabilityTable');
		
		// Get the actual availability for this property 
		$this->availability = $table->load($this->item->id);	

		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Get availability as an array of days
		$this->availability_array = HelloWorldHelper::getAvailabilityByDay($availability = $this->availability);
		
		// Build the calendar taking into account current availability...
		$this->calendar =	HelloWorldHelper::getAvailabilityCalendar($months=18, $availability = $this->availability_array);		

		// Assign the Data
		$this->form = $form;
		
		// Set the toolbar
		$this->addToolBar();

		// Set the custom script
		$this->script = $script;
		
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
		JToolBarHelper::title($isNew ? JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_NEW') : JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->form->getValue('greeting')), 'helloworld');
		
    // Built the actions for new and existing records.
		JToolBarHelper::apply('availability.apply', 'JTOOLBAR_APPLY');	
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
		$document->setTitle($isNew ? JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING') : JText::_('COM_HELLOWORLD_HELLOWORLD_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_helloworld/views/helloworld/submitbutton.js");
		$document->addStyleSheet("/administrator/components/com_helloworld/css/availability.css",'text/css',"screen");
		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
	}
}
