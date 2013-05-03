<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewProperty extends JViewLegacy
{
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null)
	{

    // Get the model state
    $this->state = $this->get('State');

  	// get the Data
		$this->form = $this->get('Form');

		$this->item = $this->get('Item');

		$this->script = $this->get('Script');

    $this->units = $this->get('Units');

    // Update the progress...this is stored in the session scope so doesn't return here.
    //HelloWorldHelper::setPropertyProgress($this->item, $this->units);

		$languages = HelloWorldHelper::getLanguages();
		$lang = HelloWorldHelper::getLang();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Assign the language data
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

		// Eventually figured out that the below hides the submenu on this view.
		//JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;

    // Get component level permissions
		$canDo = HelloWorldHelper::getActions();

    JApplication::setUserState('title'.$this->item->id, $this->item->title);

    JToolBarHelper::title($isNew ? JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_NEW') : JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->item->title, $this->item->id), 'helloworld');

    // Built the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::apply('property.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('property.save', 'JTOOLBAR_SAVE');
				//JToolBarHelper::custom('helloworld.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('property.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit.own'))
        JToolBarHelper::cancel('property.cancel', 'JTOOLBAR_CLOSE');

			{
				// We can save the new record
				JToolBarHelper::apply('property.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('property.save', 'JTOOLBAR_SAVE');
			}
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
		$document->setTitle($isNew ? JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_NEW') : JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->item->title, $this->item->id), 'helloworld');
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/submitbutton.js");

    $document->addScript("http://maps.googleapis.com/maps/api/js?key=AIzaSyAwnosMJfizqEmuQs-WsJRyHKqEsU9G-DI&sensor=true");
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/locate.js",'text/javascript',true, false);
    //$document->addScript("http://help.frenchconnections.co.uk/JavaScript.ashx?fileMask=Optional/ChatScripting",'text/javascript',false, false);

    $document->addStyleSheet(JURI::root() . "/administrator/components/com_helloworld/css/helloworld.css",'text/css',"screen");

		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
	}
}
