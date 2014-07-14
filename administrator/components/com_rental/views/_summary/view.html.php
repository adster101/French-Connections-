<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewSummary extends JViewLegacy
{
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null) 
	{

    $this->state = $this->get('State');
    
		// get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');
		$script = $this->get('Script');
		$languages = RentalHelper::getLanguages();
		$lang = RentalHelper::getLang();
	
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Assign the Data
		$this->form = $form;
		$this->item = $item;
		$this->script = $script;
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
		
    $published = $this->item->published;
    
    // Get the progress for this property 
    RentalHelper::setPropertyProgress($this->item->id,$published );
    
    // Add the submenu to the view
    RentalHelper::addSubmenu($view, $published, $this->item->property_id);
		
		// Eventually figured out that the below hides the submenu on this view.
		//JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;
    
    // Get component level permissions
		$canDo = $this->state->get('actions.permissions',array());
    
    JApplication::setUserState('title'.$this->item->id, $this->item->title);
    
    JToolBarHelper::title($isNew ? JText::_('COM_RENTAL_MANAGER_HELLOWORLD_NEW') : JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_EDIT', $this->item->title), 'helloworld');
		
    // Built the actions for new and existing records.
		if ($isNew) 
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create')) 
			{
				JToolBarHelper::apply('helloworld.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('helloworld.save', 'JTOOLBAR_SAVE');
				//JToolBarHelper::custom('helloworld.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('helloworld.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit.own'))
			{
				// We can save the new record
				JToolBarHelper::apply('helloworld.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('helloworld.save', 'JTOOLBAR_SAVE');
			}
			JToolBarHelper::cancel('helloworld.cancel', 'JTOOLBAR_CANCEL');
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
		$document->setTitle($isNew ? JText::_('COM_RENTAL_HELLOWORLD_CREATING') : JText::_('COM_RENTAL_HELLOWORLD_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_rental/js/submitbutton.js");

    $document->addScript("http://maps.googleapis.com/maps/api/js?key=AIzaSyAwnosMJfizqEmuQs-WsJRyHKqEsU9G-DI&sensor=true");
    $document->addScript(JURI::root() . "/administrator/components/com_rental/js/locate.js",'text/javascript',true, true);

    $document->addStyleSheet(JURI::root() . "/administrator/components/com_rental/css/helloworld.css",'text/css',"screen");

		JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
	}
}
