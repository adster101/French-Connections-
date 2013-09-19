<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class EnquiriesViewEnquiries extends JViewLegacy {

  protected $items;
  protected $state;
  protected $pagination;

  function display($tpl = null) {    // Gets the info from the model and displays the template 

    /*
     * Get the permissions for this component
     */
    $canDo = EnquiriesHelper::getActions();

    // Get data from the model
    $this->items = $this->get('Items');

    $this->state = $this->get('State');

    $this->pagination = $this->get('Pagination');

    $view = strtolower(JRequest::getVar('view'));

    $this->addSubMenu($canDo);

    $this->addToolBar($canDo);

    $this->setDocument();
    

    parent::display($tpl);
  }

  /**
   * Adds the submenu details for this view
   */
  protected function addSubMenu($canDo) {

    if ($canDo->get('core.edit.state')) {

      JHtmlSidebar::addFilter(
              JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', EnquiriesHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.published'), true)
      );
    }

    HelloWorldHelper::addSubmenu('enquiries');

    $this->sidebar = JHtmlSidebar::render();
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {


    $document = JFactory::getDocument();

    // Set the title property
    $this->title = JText::_('COM_ENQUIRIES_ENQUIRIES_MANAGE');

    // Set the document title
    $this->document->setTitle($this->title);

    // Set the component toolbar title
    JToolbarHelper::title($this->title);
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar($canDo) {


    if ($canDo->get('core.edit')) {
      JToolBarHelper::editList('enquiry.edit', 'JTOOLBAR_EDIT');
    }

    if ($canDo->get('core.edit.state')) {
      JToolBarHelper::publish('enquiries.publish', 'COM_ENQUIRIES_MARK_AS_READ', true);
      JToolBarHelper::unpublish('enquiries.unpublish', 'COM_ENQUIRIES_MARK_AS_UNREAD', true);
      JToolBarHelper::trash('enquiries.trash');
    }

    if ($canDo->get('core.delete')) {
      JToolBarHelper::deleteList('Are you sure?', 'enquiries.delete', 'JTOOLBAR_DELETE');
    }

    if ($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_enquiries');
    }

    JToolBarHelper::help('COM_SPECIALOFFERS_COMPONENT_HELP_VIEW', true);
  }

  /**
   * Returns an array of fields the table can be sorted by
   *
   * @return  array  Array containing the field name to sort by as the key and display text as value
   *
   * @since   3.0
   */
  protected function getSortFields() {
    return array(
        'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
        'a.title' => JText::_('JGLOBAL_TITLE'),
        'a.id' => JText::_('JGRID_HEADING_ID')
    );
  }

}
