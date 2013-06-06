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
class PaymentsViewPayment extends JViewLegacy {

  protected $items;
  protected $pagination;
  protected $state;

  /**
   * Display the view
   */
  public function display($tpl = null) {

    $app = JFactory::getApplication();
    $this->id = $app->input->get('invoice_id','','int');

    $this->state = $this->get('State');

    $this->items = $this->get('Items');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      throw new Exception(implode("\n", $errors));
    }

    PaymentsHelper::addSubmenu('payments');

    $this->addToolbar();

    $this->sidebar = JHtmlSidebar::render();

    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar.
   *
   * @since	1.6
   */
  protected function addToolbar() {
    require_once JPATH_COMPONENT . '/helpers/payments.php';

    $state = $this->get('State');
    $canDo = PaymentsHelper::getActions();

    JToolBarHelper::title(JText::sprintf('COM_PAYMENTS_TITLE_PAYMENT',$this->id));

    JToolBarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');

    //Set sidebar action - New in 3.0
    JHtmlSidebar::setAction('index.php?option=com_payments');
  }

  protected function getSortFields() {
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
