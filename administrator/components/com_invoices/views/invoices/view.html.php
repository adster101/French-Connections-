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
class InvoicesViewInvoices extends JViewLegacy
{

  protected $items;
  protected $pagination;
  protected $state;

  /**
   * Display the view
   */
  public function display($tpl = null)
  {
    $this->state = $this->get('State');
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      throw new Exception(implode("\n", $errors));
    }

    $this->addToolbar();

    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar.
   *
   * @since	1.6
   */
  protected function addToolbar()
  {
    $user = JFactory::getUser();
    $state = $this->get('State');

    JToolBarHelper::title(JText::_('COM_INVOICES_TITLE_INVOICES'), 'invoices.png');

    $bar = JToolbar::getInstance('toolbar');

    if ($user->authorise('core.admin', 'com_invoices'))
    {
      JToolBarHelper::preferences('com_invoices');
    }
  }

  protected function getSortFields()
  {
    return array(
        'a.id' => JText::_('JGRID_HEADING_ID'),
        'a.created_by' => JText::_('COM_INVOICES_INVOICES_CREATED_BY'),
        'a.date_created' => JText::_('COM_INVOICES_INVOICES_DATE_CREATED'),
        'a.total_net' => JText::_('COM_INVOICES_INVOICES_TOTAL_NET'),
        'a.vat' => JText::_('COM_INVOICES_INVOICES_VAT'),
        'a.state' => JText::_('JSTATUS'),
        'a.property_id' => JText::_('COM_INVOICES_INVOICES_PROPERTY_ID'),
        'a.due_date' => JText::_('COM_INVOICES_INVOICES_DUE_DATE'),
        'a.first_name' => JText::_('COM_INVOICES_INVOICES_FIRST_NAME'),
        'a.surname' => JText::_('COM_INVOICES_INVOICES_SURNAME'),
    );
  }

}

