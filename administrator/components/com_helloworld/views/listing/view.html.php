<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class HelloWorldViewListing extends JViewLegacy {

  protected $state;

  /**
   * Listing view display method
   * @return void
   */
  function display($tpl = null) {


    // Find the user details
    $user = JFactory::getUser();
    $userID = $user->id;

    // Get the ID
    $app = JFactory::getApplication();
    $this->id = $app->input->get('id', '', 'int');

    // Get data from the model
    $this->items = $this->get('Items');

    $this->pagination = $this->get('Pagination');

    $this->state = $this->get('State');

    $this->form = $this->getForm();

    // Register the JHtmlProperty class
    JLoader::register('JHtmlProperty', JPATH_COMPONENT . '/helpers/html/property.php');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
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
  protected function addToolBar() {

    $document = JFactory::getDocument();

    $user = JFactory::getUser();

    $canDo = HelloWorldHelper::getActions();

    JToolBarHelper::title(count($this->items) > 0 ? JText::sprintf('COM_HELLOWORLD_HELLOWORLD_LISTING_TITLE', $this->id) : 'No listings');

    JToolBarHelper::cancel('properties');



    // Only show the add units button if there is at least one listing
    if (count($this->items) > 0) {
      if ($canDo->get('core.delete')) {
        JToolBarHelper::deleteList('', 'units.delete', 'JTOOLBAR_DELETE');
      }

      if ($canDo->get('core.create')) {
        JToolBarHelper::addNew('unitversions.edit', 'COM_HELLOWORLD_HELLOWORLD_ADD_NEW_UNIT', false);
      }

      if ($canDo->get('helloworld.property.preview')) {
        JToolBarHelper::preview('/component/accommodation/?view=property&id=' . (int) $this->id);
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
  protected function setDocument() {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_HELLOWORLD_ADMINISTRATION'));
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/submitbutton.js");

    JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
  }

}
