<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class HelloWorldViewRenewal extends JViewLegacy {

  /**
   * HelloWorld raw view display method
   * This is used to check how many properties the user has
   * when they click 'new' on the property manager.
   * 
   * @return void
   */
  function display($tpl = null) {

    $app = JFactory::getApplication();
    $input = $app->input;

    $this->extension = $input->get('option', '', 'string');

    $this->id = $input->get('id', '', 'int');

    // Get the record details...again...this time from the session
    $this->listing = JApplication::getUserState($this->extension . '.listing.detail', '');

    // Get the amount they actually need to pay...
    $this->units = $this->get('Units');
    
    // Get the payment/address form
    $this->form = $this->get('Form');

    // Get a total of what the owner needs to pay for this renewal
    // Set the document
    $this->setDocument();

    // Set the document
    $this->addToolBar();

    // Display the template
    parent::display($tpl);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_HELLOWORLD_ADMINISTRATION'));
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/submitbutton.js", true, false);
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/vat.js", 'text/javascript', true, false);

    JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
    // Get component level permissions
    $canDo = HelloWorldHelper::getActions();
    
    // Display a helpful navigation for the owners 
    if ($canDo->get('helloworld.ownermenu.view')) {

      $view = strtolower(JRequest::getVar('view'));

      $canDo = HelloWorldHelper::addSubmenu($view);

      // Add the side bar
      $this->sidebar = JHtmlSidebar::render();
    }

    JToolBarHelper::cancel('helloworld.cancel', 'JTOOLBAR_CLOSE');

    JToolBarHelper::help('COM_HELLOWORLD_HELLOWORLD_NEW_PROPERTY_HELP_VIEW', true);
    
  }

}
