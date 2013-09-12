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
class VouchersViewVoucher extends JViewLegacy {

  protected $items;
  protected $pagination;
  protected $state;

  /**
   * Display the view
   */
  public function display($tpl = null) {

    $app = JFactory::getApplication();

    $this->id = $app->input->get('id', '', 'int');

    $this->form = $this->get('Form');
    
    $this->state = $this->get('State');

    $this->items = $this->get('Items');

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
  protected function addToolbar($canDo) {

    JToolBarHelper::title(JText::sprintf('COM_INVOICES_TITLE_INVOICE_LINES', $this->id), 'invoice_lines.png');

    if ($canDo->get('core.create')) {
      JToolBarHelper::apply('voucher.apply', 'JTOOLBAR_APPLY');
      JToolBarHelper::save('voucher.save', 'JTOOLBAR_SAVE');
      JToolBarHelper::save2new('voucher.save');
    }

    JToolBarHelper::cancel('voucher.cancel', 'JTOOLBAR_CLOSE');
  }

}
