<?php

/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Invoices.
 */
class TicketsViewTickets extends JViewLegacy {

  protected $items;
  protected $pagination;
  protected $state;

  /**
   * Display the view
   */
  public function display($tpl = null) {

    $this->state = $this->get('State');
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      throw new Exception(implode("\n", $errors));
    }

    $canDo = TicketsHelper::getActions();

    TicketsHelper::addSubmenu('tickets');

    $this->addToolbar($canDo);

    $this->sidebar = JHtmlSidebar::render();
    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar.
   *
   * @since	1.6
   */
  protected function addToolbar($canDo = '') {

    JToolBarHelper::title(JText::_('COM_TICKETS_TICKETS_TITLE'));
    $user = JFactory::getUser();

    //Check if the form exists before showing the add/edit buttons
    $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/ticket.xml';
    if (file_exists($formPath)) {

      if ($canDo->get('core.create')) {
        JToolBarHelper::addNew('ticket.add', 'JTOOLBAR_NEW');
      }

      if ($canDo->get('core.edit') && isset($this->items[0])) {
        JToolBarHelper::editList('ticket.edit', 'JTOOLBAR_EDIT');
      }
    }

    if ($canDo->get('core.edit.state')) {

      if (isset($this->items[0]->state)) {
        JToolBarHelper::divider();
        JToolBarHelper::custom('tickets.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::custom('tickets.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
      } else if (isset($this->items[0])) {
        //If this component does not use state then show a direct delete button as we can not trash
        JToolBarHelper::deleteList('', 'tickets.delete', 'JTOOLBAR_DELETE');
      }

      if (isset($this->items[0]->state)) {
        JToolBarHelper::divider();
        JToolBarHelper::archiveList('tickets.archive', 'JTOOLBAR_ARCHIVE');
      }

      if (isset($this->items[0]->checked_out)) {
        JToolBarHelper::custom('tickets.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
      }
    }

    //Show trash and delete for components that uses the state field
    if (isset($this->items[0]->state)) {
      if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
        JToolBarHelper::deleteList('', 'tickets.delete', 'JTOOLBAR_EMPTY_TRASH');
        JToolBarHelper::divider();
      } else if ($canDo->get('core.edit.state')) {
        JToolBarHelper::trash('tickets.trash', 'JTOOLBAR_TRASH');
        JToolBarHelper::divider();
      }
    }

    // Add a batch button
    if ($user->authorise('core.create', 'com_tickets') && $user->authorise('core.edit', 'com_tickets') && $user->authorise('core.edit.state', 'com_tickets')) {
      // Get the toolbar object instance
      $bar = JToolBar::getInstance('toolbar');
      JHtml::_('bootstrap.modal', 'collapseModal');
      $title = JText::_('JTOOLBAR_BATCH');

      // Instantiate a new JLayoutFile instance and render the batch button
      $layout = new JLayoutFile('joomla.toolbar.batch');

      $dhtml = $layout->render(array('title' => $title));
      $bar->appendButton('Custom', $dhtml, 'batch');
    }

    if ($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_tickets');
    }

    //Set sidebar action - New in 3.0
    JHtmlSidebar::setAction('index.php?option=com_voucher&view=tickets');

    JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', TicketsHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'), true)
    );
    JHtmlSidebar::addFilter(
            JText::_('COM_TICKETS_AREA'), 'filter_area', JHtml::_('select.options', JHtml::_('category.options', 'com_tickets'), 'value', 'text', $this->state->get('filter.area'), true)
    );

    JHtmlSidebar::addFilter(
            JText::_('COM_TICKETS_SEVERITY'), 'filter_severity', JHtml::_('select.options', TicketsHelper::getSeverities(), 'value', 'text', $this->state->get('filter.severity'), true)
    );
  }

}
