<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewImages extends JView
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
		JToolBarHelper::title($isNew ? JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_NEW') : JText::sprintf('COM_HELLOWORLD_IMAGES_EDIT', $this->form->getValue('greeting')), 'helloworld');

    // Add an upload button?
    //JToolBarHelper::media_manager( '44' );

    // Built the actions for new and existing records.
		JToolBarHelper::apply('images.apply', 'JTOOLBAR_APPLY');	
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
		$document->addScript(JURI::root() . "administrator/components/com_helloworld/views/images/submitbutton.js");
		$document->addScript(JURI::root() . "administrator/components/com_helloworld/js/Request.File.js");
		$document->addScript(JURI::root() . "administrator/components/com_helloworld/js/Form.MultipleFileInput.js");
		$document->addScript(JURI::root() . "administrator/components/com_helloworld/js/Form.Upload.js");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/upload.css",'text/css',"screen");
		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
	}
}
