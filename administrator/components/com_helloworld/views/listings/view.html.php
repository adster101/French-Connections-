<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class HelloWorldViewListings extends JViewLegacy {

  protected $state;

  /**
   * HelloWorlds view display method
   * @return void
   */
  function display($tpl = null) {
    // Find the user details
    $user = JFactory::getUser();
    $userID = $user->id;

    // Get data from the model
    $items = $this->get('Items');

    $pagination = $this->get('Pagination');

    $this->state = $this->get('State');

    // Assign data to the view
    $this->items = $items;
    $this->pagination = $pagination;

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }

    // Preprocess the list of items to find ordering divisions.
    foreach ($this->items as &$item) {
      // $this->ordering[$item->parent_id][] = $item->id;
    }

    // Register the JHtmlProperty class
    JLoader::register('JHtmlProperty', JPATH_COMPONENT . '/helpers/html/property.php');

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
    $document->addStyleDeclaration('.icon-48-helloworld {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');

    $user = JFactory::getUser();

    // Here we register a new JButton which simply uses the ajax squeezebox rather than the iframe handler
    JLoader::register('JToolbarButtonAjaxpopup', JPATH_ROOT . '/administrator/components/com_helloworld/buttons/Ajaxpopup.php');

    // Here we register a new JButton which simply uses the ajax squeezebox rather than the iframe handler
    JLoader::register('JToolbarButtonAjaxpopupchooseowner', JPATH_ROOT . '/administrator/components/com_helloworld/buttons/Ajaxpopupchooseowner.php');

    $canDo = HelloWorldHelper::getActions();

    JToolBarHelper::title(JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLDS'), 'helloworld');
    if ($canDo->get('core.create')) {
      JToolBarHelper::addNew('propertyversions.edit', 'COM_HELLOWORLD_HELLOWORLD_ADD_NEW_PROPERTY', false);
    }
    if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
      JToolBarHelper::editList('propertyversions.edit', 'JTOOLBAR_EDIT');
    }
    if ($canDo->get('core.delete')) {
      JToolBarHelper::deleteList('', 'listings.delete', 'JTOOLBAR_DELETE');
    }

    if ($canDo->get('core.edit.state')) {
      JToolBarHelper::publish('listings.publish', 'JTOOLBAR_PUBLISH', true);
      JToolBarHelper::unpublish('listings.unpublish', 'JTOOLBAR_UNPUBLISH', true);
      JToolBarHelper::trash('listings.trash');
    }

    if ($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_helloworld');
    }

    // Check that the user is authorised to view the filters.
    if ($canDo->get('helloworld.filter')) {

      JHtmlSidebar::addFilter(
              JText::_('COM_HELLOWORLD_HELLOWORLD_FILTER_ACTIVE'), 'filter_published', JHtml::_('select.options', HelloWorldHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.published'), true)
      );
      JHtmlSidebar::addFilter(
              JText::_('COM_HELLOWORLD_HELLOWORLD_FILTER_REVIEW'), 'filter_review', JHtml::_('select.options', HelloWorldHelper::getReviewOptions(), 'value', 'text', $this->state->get('filter.review'), true)
      );
      JHtmlSidebar::addFilter(
              JText::_('COM_HELLOWORLD_HELLOWORLD_FILTER_SNOOZED'), 'filter_snoozed', JHtml::_('select.options', HelloWorldHelper::getSnoozeOptions(), 'value', 'text', $this->state->get('filter.snoozed'), true)
      );

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
