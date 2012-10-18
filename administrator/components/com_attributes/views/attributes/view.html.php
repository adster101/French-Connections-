<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class AttributesViewAttributes extends JViewLegacy {

  protected $pagination;

  function display($tpl = null) {    // Gets the info from the model and displays the template 
    // Get data from the model
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');
    $this->state = $this->get('State');

    $this->setDocument();
    
    		$view = strtolower(JRequest::getVar('view'));

    
    AttributesHelper::addSubmenu($view);

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
    $document->setTitle(JText::_('Manage Property Attributes'));
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
    $document = JFactory::getDocument();

    JToolBarHelper::addNew('attribute.add', 'JTOOLBAR_NEW');
    JToolBarHelper::editList('attribute.edit', 'JTOOLBAR_EDIT');
    JToolBarHelper::publish('attributes.publish', 'JTOOLBAR_PUBLISH', true);
    JToolBarHelper::unpublish('attributes.unpublish', 'JTOOLBAR_UNPUBLISH', true);
    JToolBarHelper::trash('attributes.trash');
    JToolBarHelper::deleteList('', 'classifications.delete', 'JTOOLBAR_DELETE');

    // Set the title which appears on the toolbar 
    JToolBarHelper::title(JText::_('Manage Property Attributes'));
  }

}
