<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class FeaturedPropertiesViewFeaturedPropertyTypes extends JViewLegacy {

  protected $pagination;

  function display($tpl = null) {    // Gets the info from the model and displays the template 
    // Get data from the model
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');
    $this->state = $this->get('State');

    $this->setDocument();

    $view = strtolower(JRequest::getVar('view'));
    
    FeaturedPropertiesHelper::addSubmenu('featuredpropertytypes');

    $this->addToolBar();
    $this->sidebar = JHtmlSidebar::render();


    parent::display($tpl);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('Manage Property Attribute Types'));
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {

    $document = JFactory::getDocument();

    JToolBarHelper::addNew('featuredpropertytype.add', 'JTOOLBAR_NEW');
    JToolBarHelper::editList('featuredpropertytype.edit', 'JTOOLBAR_EDIT');
    JToolBarHelper::publish('featuredpropertytypes.publish', 'JTOOLBAR_PUBLISH', true);
    JToolBarHelper::unpublish('featuredpropertytypes.unpublish', 'JTOOLBAR_UNPUBLISH', true);
    JToolBarHelper::trash('featuredpropertytypes.trash');

    // Set the title which appears on the toolbar 
    JToolBarHelper::title(JText::_('Manage Property Attribute Types'));
  }

}
