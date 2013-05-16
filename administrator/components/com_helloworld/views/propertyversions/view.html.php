<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewPropertyVersions extends JViewLegacy
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

    // Get an instance of our model, setting ignore_request to true so we bypass units->populateState
    $model = JModelLegacy::getInstance('Listing', 'HelloWorldModel',array('ignore_request'=>true));

    // Here we attempt to wedge some data into the model
    // So another method in the same model can use it.
    $listing_id = ($this->item->parent_id) ? $this->item->parent_id : '';

    // Set some model options
    $model->setState('com_helloworld.' . $model->getName() . '.id', $listing_id);
    $model->setState('list.limit', 10);

    $this->progress = $model->getItems();

		$languages = HelloWorldHelper::getLanguages();
		$lang = HelloWorldHelper::getLang();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

    // Register the JHtmlProperty class
    JLoader::register('JHtmlProperty', JPATH_COMPONENT . '/helpers/html/property.php');

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
		// Determine the view we are using.
		$view = strtolower(JRequest::getVar('view'));

    // Get the user details
		$user = JFactory::getUser();
		$userId = $user->id;

    // Is this a new property?
		$isNew = $this->item->id == 0;

    // Get component level permissions
		$canDo = HelloWorldHelper::getActions();

    JToolBarHelper::title($isNew ? JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_NEW') : JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->item->title, $this->item->id), 'helloworld');

    // Built the actions for new and existing records.
		if ($isNew)
		{
			JToolBarHelper::cancel('property.cancel', 'JTOOLBAR_CANCEL');

			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::save('propertyversions.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::apply('propertyversions.apply', 'JTOOLBAR_APPLY');
				//JToolBarHelper::custom('helloworld.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
		}
		else
		{
			if ($canDo->get('core.edit.own'))
        JToolBarHelper::cancel('propertyversions.cancel', 'JTOOLBAR_CLOSE');

			{
				// We can save the new record
				JToolBarHelper::apply('propertyversions.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('propertyversions.save', 'JTOOLBAR_SAVE');
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
