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
class PaymentsViewPayments extends JViewLegacy
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
    protected function addToolbar()
    {
        require_once JPATH_COMPONENT . '/helpers/payments.php';

        $state = $this->get('State');
        $canDo = PaymentsHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_PAYMENTS_TITLE_PAYMENTS'));


        if ($canDo->get('core.edit.state'))
        {

            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
            }
            else if (isset($this->items[0]))
            {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'payments.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('payments.archive', 'JTOOLBAR_ARCHIVE');
            }
        }

        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_payments');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_payments&view=payments');
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
