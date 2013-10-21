<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld View
 */
class HelloWorldViewPropertyversions extends JViewLegacy {

  public function __construct($config = array()) {
    parent::__construct($config);

    $this->_name = 'propertyversions';
  }

  /**
   * display method of Hello view
   * @return void
   */
  public function display($tpl = null) {

    // Get the model state
    $this->state = $this->get('State');

    // get the Data
    $this->form = $this->get('Form');

    $this->item = $this->get('Item');

    $this->script = $this->get('Script');

    // Get an instance of our model, setting ignore_request to true so we bypass units->populateState
    $model = JModelLegacy::getInstance('Listing', 'HelloWorldModel', array('ignore_request' => true));
    
    // Switch to the revised listing class
    // $model = JModelLegacy::getInstance('Listing_proper', 'HelloWorldModel', array('id' => $this->item->property_id));

    // Here we attempt to wedge some data into the model
    // So another method in the same model can use it.
    $listing_id = ($this->item->property_id) ? $this->item->property_id : '';

    // Set some model options
    $model->setState('com_helloworld.' . $model->getName() . '.id', $listing_id);
    $model->setState('list.limit', 100);

    $this->progress = $model->getItems();

    $languages = HelloWorldHelper::getLanguages();
    $lang = HelloWorldHelper::getLang();

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
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
  protected function addToolBar() {
    // Determine the view we are using.
    $view = strtolower(JRequest::getVar('view'));

    // Get the user details
    $user = JFactory::getUser();
    $userId = $user->id;

    // Is this a new property?
    $isNew = $this->item->id == 0;

    // Get component level permissions
    $canDo = HelloWorldHelper::getActions();

    JToolBarHelper::title(JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->item->property_id));

    // Built the actions for new and existing records.
    if ($isNew) {
      JToolBarHelper::cancel('propertyversions.cancel', 'JTOOLBAR_CANCEL');

      // For new records, check the create permission.
      if ($canDo->get('core.create')) {
        JToolBarHelper::save('propertyversions.saveandnext', 'JTOOLBAR_SAVE');
        JToolBarHelper::apply('propertyversions.apply', 'JTOOLBAR_APPLY');
      }
    } else {
      if ($canDo->get('core.edit.own'))
        JToolBarHelper::cancel('propertyversions.cancel', 'JTOOLBAR_CANCEL'); {
        // We can save the new record
        JToolBarHelper::apply('propertyversions.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('propertyversions.save', 'JTOOLBAR_SAVE');
        //JToolBarHelper::custom('propertyversions.saveandnext', 'next', 'next', 'COM_HELLOWORLD_HELLOWORLD_SAVE_AND_NEXT', false);

      }
    }
    
    HelloWorldHelper::addSubmenu('listings');

    // Add the side bar
    $this->sidebar = JHtmlSidebar::render();
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {

    $isNew = $this->item->id == 0;
    $document = JFactory::getDocument();

    $document->setTitle(JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->item->id));
    JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
    JText::script('COM_HELLOWORLD_HELLOWORLD_UNSAVED_CHANGES');
    $document->addScript(JURI::root() . "/media/fc/js/general.js");

    $document->addScript("http://maps.googleapis.com/maps/api/js?key=AIzaSyAwnosMJfizqEmuQs-WsJRyHKqEsU9G-DI&sensor=true");
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/locate.js", 'text/javascript', true, false);
    //$document->addScript("http://help.frenchconnections.co.uk/JavaScript.ashx?fileMask=Optional/ChatScripting",'text/javascript',false, false);

    $document->addStyleSheet(JURI::root() . "/administrator/components/com_helloworld/css/helloworld.css", 'text/css', "screen");


  }

}
