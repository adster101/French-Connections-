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
class VouchersViewVouchers extends JViewLegacy {

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
    
    $canDo = VouchersHelper::getActions();

    InvoicesHelper::addSubmenu('vouchers');

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


    JToolBarHelper::title(JText::_('COM_INVOICES_TITLE_INVOICES'), 'invoices.png');

    //Check if the form exists before showing the add/edit buttons
    $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/voucher.xml';
    if (file_exists($formPath)) {

      if ($canDo->get('core.create')) {
        JToolBarHelper::addNew('voucher.add', 'JTOOLBAR_NEW');
      }

      if ($canDo->get('core.edit') && isset($this->items[0])) {
        JToolBarHelper::editList('voucher.edit', 'JTOOLBAR_EDIT');
      }
    }

    if ($canDo->get('core.edit.state')) {

      if (isset($this->items[0]->state)) {
        JToolBarHelper::divider();
        JToolBarHelper::custom('vouchers.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::custom('vouchers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
      } else if (isset($this->items[0])) {
        //If this component does not use state then show a direct delete button as we can not trash
        JToolBarHelper::deleteList('', 'vouchers.delete', 'JTOOLBAR_DELETE');
      }

      if (isset($this->items[0]->state)) {
        JToolBarHelper::divider();
        JToolBarHelper::archiveList('vouchers.archive', 'JTOOLBAR_ARCHIVE');
      }
      if (isset($this->items[0]->checked_out)) {
        JToolBarHelper::custom('vouchers.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
      }
    }

    //Show trash and delete for components that uses the state field
    if (isset($this->items[0]->state)) {
      if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
        JToolBarHelper::deleteList('', 'vouchers.delete', 'JTOOLBAR_EMPTY_TRASH');
        JToolBarHelper::divider();
      } else if ($canDo->get('core.edit.state')) {
        JToolBarHelper::trash('vouchers.trash', 'JTOOLBAR_TRASH');
        JToolBarHelper::divider();
      }
    }

    if ($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_vouchers');
    }

    //Set sidebar action - New in 3.0
    JHtmlSidebar::setAction('index.php?option=com_voucher&view=vouchers');

    JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
    );

  
  }

  protected function getSortFields() {
    return array(
        'a.id' => JText::_('JGRID_HEADING_ID'),
        'a.date_created' => JText::_('COM_INVOICES_INVOICES_DATE_CREATED'),
        'a.state' => JText::_('JSTATUS'),
        'a.property_id' => JText::_('COM_INVOICES_INVOICES_PROPERTY_ID'),
        'a.due_date' => JText::_('COM_INVOICES_INVOICES_DUE_DATE')
    );
  }

}
