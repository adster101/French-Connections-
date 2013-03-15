<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewUnit extends JViewLegacy
{
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null) 
	{

    $this->state = $this->get('State');

		
    // get and assign the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
    
    $this->script = $this->get('Script');
    
   
    $this->languages = HelloWorldHelper::getLanguages();
		$this->lang = HelloWorldHelper::getLang();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

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
		
    
    // Get the progress for this property 
    //HelloWorldHelper::setPropertyProgress($this->item->id,$published );
    
 		
		// Eventually figured out that the below hides the submenu on this view.
		//JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
    
		$isNew = $this->item->id == 0;
    
    // Get component level permissions
		$canDo = HelloWorldHelper::getActions();

    // Get the listing details from the session...
    $listing = JApplication::getUserState('listing', false);

    JToolBarHelper::title($listing->listing_title ? JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $listing->listing_title,$listing->listing_id) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'));
		
    // Built the actions for new and existing records.
		if ($isNew) 
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create')) 
			{
				JToolBarHelper::apply('unit.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('unit.save', 'JTOOLBAR_SAVE');
				//JToolBarHelper::custom('helloworld.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('property.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit.own'))
			{
				// We can save the new record
				JToolBarHelper::apply('unit.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('unit.save', 'JTOOLBAR_SAVE');
			}
			JToolBarHelper::cancel('property.cancel', 'JTOOLBAR_CLOSE');
		} 
    
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
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
	  
    // Get the listing details from the session...
    $listing = JApplication::getUserState('listing', false);

    $document->setTitle($listing->listing_title ? JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $listing->listing_title,$listing->listing_id) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/submitbutton.js");

    $document->addScript("http://maps.googleapis.com/maps/api/js?key=AIzaSyAwnosMJfizqEmuQs-WsJRyHKqEsU9G-DI&sensor=true");
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/locate.js",'text/javascript',true, true);

    $document->addStyleSheet(JURI::root() . "/administrator/components/com_helloworld/css/helloworld.css",'text/css',"screen");

		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
	}
}
