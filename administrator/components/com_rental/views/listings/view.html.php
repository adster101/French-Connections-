<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class RentalViewListings extends JViewLegacy
{

  protected $items;
  protected $pagination;
  protected $state;

  /**
   * HelloWorlds view display method
   * @return void
   */
  function display($tpl = null)
  {
    // Find the user details
    $user = JFactory::getUser();
    $userID = $user->id;

    // Get data from the model  
    $this->pagination = $this->get('Pagination');
    $this->items = $this->get('Items');
    $this->state = $this->get('State');
    $this->filterForm = $this->get('FilterForm');
    $this->activeFilters = $this->get('ActiveFilters');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }

    // Preprocess the list of items to find ordering divisions.
    foreach ($this->items as &$item)
    {
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
  protected function addToolBar()
  {

    $canDo = RentalHelper::getActions();

    JToolBarHelper::title(JText::_('COM_RENTAL_MANAGER_HELLOWORLDS'), 'helloworld');

    if ($canDo->get('core.create'))
    {
      JToolBarHelper::addNew('propertyversions.add', 'COM_RENTAL_HELLOWORLD_ADD_NEW_PROPERTY', false);
    }

    if ($canDo->get('core.edit') || ($canDo->get('core.edit.own')))
    {
      JToolBarHelper::editList('propertyversions.edit', 'JTOOLBAR_EDIT');
    }

    if ($canDo->get('core.edit.state'))
    {
      JToolBarHelper::publish('listings.publish', 'JTOOLBAR_PUBLISH', true);
      JToolBarHelper::unpublish('listings.unpublish', 'JTOOLBAR_UNPUBLISH', true);
      JToolBarHelper::trash('listings.trash');
    }

    // Add the custom snooze button
    if ($canDo->get('rental.listing.admin'))
    {
      JToolbarHelper::custom('property.edit', 'refresh', '', 'COM_RENTAL_UPDATE_PROPERTY', true);
    }

    if ($canDo->get('core.admin'))
    {
      JToolBarHelper::preferences('com_rental');
    }

    $view = strtolower(JRequest::getVar('view'));


    // Add the side bar
    // $this->sidebar = JHtmlSidebar::render();
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_RENTAL_ADMINISTRATION'));
    $document->addScript("media/fc/js/general.js", 'text/javascript', true);

    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
  }

}
