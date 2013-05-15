<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewUnitVersions extends JViewLegacy
{
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null)
	{

    $this->state = $this->get('State');

    // Get the unit item...
		$this->item = $this->get('Item');

    // Get an instance of our model, setting ignore_request to true so we bypass units->populateState
    $model = JModelLegacy::getInstance('Units', 'HelloWorldModel',array('ignore_request'=>true));

    // Here we attempt to wedge some data into the model
    // So another method in the same model can use it.
    $listing_id = ($this->item->parent_id) ? $this->item->parent_id : '';

    // Set some model options
    $model->setState('com_helloworld.' . $model->getName() . '.id', $listing_id);
    $model->setState('list.limit', 10);

    // Get the unit progress...
    $this->progress = $model->getItems();

    // Get the unit edit form
		$this->form = $this->get('Form');

    $this->languages = HelloWorldHelper::getLanguages();
		$this->lang = HelloWorldHelper::getLang();

    // Register the JHtmlProperty class
    JLoader::register('JHtmlProperty', JPATH_COMPONENT . '/helpers/html/property.php');

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

		$user = JFactory::getUser();
		$userId = $user->id;

		$isNew = $this->item->id == 0;

    // Get component level permissions
		$canDo = HelloWorldHelper::getActions();

    JToolBarHelper::title(($isNew) ? JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_UNIT_EDIT') : JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->item->unit_title)  );

    // Built the actions for new and existing records.
		if ($isNew)
		{
			JToolBarHelper::cancel('unitversions.cancel', 'JTOOLBAR_CANCEL');
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::apply('unitversions.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('unitversions.save', 'JTOOLBAR_SAVE');
				//JToolBarHelper::custom('helloworld.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
		}
		else
		{
			JToolBarHelper::cancel('unitversions.cancel', 'JTOOLBAR_CLOSE');
			if ($canDo->get('core.edit.own'))
			{
				// We can save the new record
				JToolBarHelper::save('unitversions.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::apply('unitversions.apply', 'JTOOLBAR_APPLY');
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
    $document->setTitle($isNew ? JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_UNIT_EDIT') : JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->item->unit_title) );
		$document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/submitbutton.js");
		$document->addScript(JURI::root() . "/administrator/components/com_helloworld/models/forms/helloworld.js");
    $document->addStyleSheet(JURI::root() . "/administrator/components/com_helloworld/css/helloworld.css",'text/css',"screen");

		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
	}
}
