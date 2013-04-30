<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class HelloWorldViewAutoRenewals extends JViewLegacy {

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

    // Get the property listing details...
    $this->item = $this->get('Item');

    // Get the auto renewal options for this listing
    $this->form = $this->get('Form');

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

    JToolBarHelper::title(($this->item->title) ? JText::sprintf('COM_HELLOWORLD_HELLOWORLD_MANAGE_AUTO_RENEWAL', $this->item->id, $this->item->title) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_NEW'));

    // Get component level permissions
		$canDo = HelloWorldHelper::getActions();

    // Display a helpful navigation for the owners 
    if ($canDo->get('helloworld.ownermenu.view')) {

      $view = strtolower(JRequest::getVar('view'));

      HelloWorldHelper::addSubmenu($view);

      // Add the side bar
      $this->sidebar = JHtmlSidebar::render();
    }
    
    if ($canDo->get('helloworld.property.autorenew')) {
      // We can save the new record
      JToolBarHelper::save('autorenewals.save', 'JTOOLBAR_SAVE');
    }
    
    
    JToolBarHelper::cancel('autorenewals.cancel', 'JTOOLBAR_CLOSE');

    JToolBarHelper::help('COM_HELLOWORLD_HELLOWORLD_NEW_PROPERTY_HELP_VIEW', true);
  }

}
