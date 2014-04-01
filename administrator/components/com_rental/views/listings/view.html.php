<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class RentalViewListings extends JViewLegacy {

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
      // $this->ordering[$item->property_id][] = $item->id;
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

    $user = JFactory::getUser();

    // Here we register a new JButton which simply uses the ajax squeezebox rather than the iframe handler
    JLoader::register('JToolbarButtonAjaxpopup', JPATH_ROOT . '/administrator/components/com_rental/buttons/Ajaxpopup.php');

    // Here we register a new JButton which simply uses the ajax squeezebox rather than the iframe handler
    JLoader::register('JToolbarButtonAjaxpopupchooseowner', JPATH_ROOT . '/administrator/components/com_rental/buttons/Ajaxpopupchooseowner.php');

    $canDo = RentalHelper::getActions();

    JToolBarHelper::title(JText::_('COM_RENTAL_MANAGER_HELLOWORLDS'), 'helloworld');
    if ($canDo->get('core.create')) {
      JToolBarHelper::addNew('propertyversions.add', 'COM_RENTAL_HELLOWORLD_ADD_NEW_PROPERTY', false);
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
      JToolBarHelper::preferences('com_rental');
    }

    // Check that the user is authorised to view the filters.
    if ($canDo->get('helloworld.filter')) {

      JHtmlSidebar::addFilter(
              JText::_('COM_RENTAL_HELLOWORLD_FILTER_ACTIVE'), 'filter_published', JHtml::_('select.options', RentalHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.published'), true)
      );
      JHtmlSidebar::addFilter(
              JText::_('COM_RENTAL_HELLOWORLD_FILTER_REVIEW'), 'filter_review', JHtml::_('select.options', RentalHelper::getReviewOptions(), 'value', 'text', $this->state->get('filter.review'), true)
      );
      JHtmlSidebar::addFilter(
              JText::_('COM_RENTAL_HELLOWORLD_FILTER_SNOOZED'), 'filter_snoozed', JHtml::_('select.options', RentalHelper::getSnoozeOptions(), 'value', 'text', $this->state->get('filter.snoozed'), true)
      );

    }

    

      $view = strtolower(JRequest::getVar('view'));

      $canDo = RentalHelper::addSubmenu($view);

      // Add the side bar
      $this->sidebar = JHtmlSidebar::render();

    



  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_RENTAL_ADMINISTRATION'));
    $document->addScript(JURI::root() . "administrator/components/com_rental/js/jquery-ui-1.8.23.custom.min.js", 'text/javascript', true);
    $document->addStyleSheet(JURI::root() . "administrator/components/com_rental/css/jquery-ui-1.8.23.custom.css", 'text/css', "screen");
    $document->addScript(JURI::root() . "media/fc/js/general.js", 'text/javascript', true);

    JText::script('COM_RENTAL_HELLOWORLD_ERROR_UNACCEPTABLE');
  }

}
