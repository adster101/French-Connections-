<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewImages extends JViewLegacy
{
	/**
	 * display method of Availability View
	 * @return void
	 */
	public function display($tpl = null) 
	{
		// Get the property ID we are editing.
		$this->item->id = JRequest::getVar('id');
    $app = JFactory::getApplication();

    // Get the custom script path for this screen
		$script = $this->get('Script');
    
    // Get the item data
    $items = $this->get('Items');

    // Assign the Item
		$this->items = $items;
    
		// Set the toolbar
		$this->addToolBar();

		// Set the custom script
		$this->script = $script;
		
    $this->state = $this->get('State');
    
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

		$user = JFactory::getUser();
		$userId = $user->id;    
   
    // Get component level permissions
		$canDo = HelloWorldHelper::getActions();

    // Get the listing details from the session...
    $listing = JApplication::getUserState('listing', false);

    JToolBarHelper::title($listing->title ? JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $listing->title,$listing->id) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'));
 
    
    // Built the actions for new and existing records.
    if ($canDo->get('core.create')) {
      JToolBarHelper::addNew('image.edit');
      
    }
    
    
    // Cancel out to the helloworld(s) default view rather than the availabilities view...??
		JToolBarHelper::cancel('images.cancel', 'JTOOLBAR_CANCEL');
 
    // Display a helpful navigation for the owners 
    if ($canDo->get('helloworld.ownermenu.view')) {
    
      $view = strtolower(JRequest::getVar('view'));
  
      $canDo = HelloWorldHelper::addSubmenu($view);
      
      // Add the side bar
      $this->sidebar = JHtmlSidebar::render();
      
    }
  }
  
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
	  
    // Get the listing details from the session...
    $listing = JApplication::getUserState('listing', false);

    $document->setTitle($listing->title ? JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $listing->title,$listing->id) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'));

    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/submitbutton.js");
		$document->addScript(JURI::root() . "administrator/components/com_helloworld/js/Request.File.js");
		$document->addScript(JURI::root() . "administrator/components/com_helloworld/js/Form.MultipleFileInput.js");
		$document->addScript(JURI::root() . "administrator/components/com_helloworld/js/Form.Upload.js");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/upload.css",'text/css',"screen");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/bootstrap-button.css",'text/css',"screen");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/helloworld.css",'text/css',"screen");

    JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
	}
}
