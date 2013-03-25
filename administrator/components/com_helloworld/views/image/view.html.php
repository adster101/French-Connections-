<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewImage extends JViewLegacy {

  /**
   * display method of Availability View
   * @return void
   */
  public function display($tpl = null) {
    // Get the property ID from the GET variable
    $this->property_id = JRequest::getVar('property_id', '', 'GET', 'int');

    // Get the image file ID of which we need to delete
    $this->file_id = JRequest::getVar('id', '', 'GET', 'int');

    // Get the caption details for the image
    $this->form = $this->get('Form');

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
    // Determine the layout we are using. 
    // Should this be done with views? 
    $view = strtolower(JRequest::getVar('view'));

    $user = JFactory::getUser();
    $userId = $user->id;

    // Get component level permissions
    $canDo = HelloWorldHelper::getActions();

    // Get the listing details from the session...
    $listing = JApplication::getUserState('listing', false);

    JToolBarHelper::title($listing->title ? JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $listing->title, $listing->id) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'));


 


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
  protected function setDocument() {
    $document = JFactory::getDocument();

    // Get the listing details from the session...
    $listing = JApplication::getUserState('listing', false);

    $document->setTitle($listing->title ? JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $listing->title, $listing->id) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'));

    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/vendor/jquery.ui.widget.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/jquery.iframe-transport.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/jquery.fileupload.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/jquery.fileupload-fp.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/jquery.fileupload-ui.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/main.js", 'text/javascript', true, false);
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/helloworld.css", 'text/css', "screen");

    JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
  }

}
