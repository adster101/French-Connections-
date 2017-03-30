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

    $this->item = $this->get('Item');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      throw new Exception(implode("\n", $errors));
    }

    $canDo = VouchersHelper::getActions();

    InvoicesHelper::addSubmenu('vouchers');

    JText::script('JGLOBAL_VALIDATION_FORM_FAILED');

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

    if ($this->id) {
      JToolBarHelper::title(JText::sprintf('COM_VOUCHERS_VOUCHER_TITLE', $this->item->property_id));
    } else {
      JToolBarHelper::title(JText::sprintf('COM_VOUCHERS_VOUCHER_NEW', $this->id));
    }

    if ($canDo->get('core.create')) {
      JToolBarHelper::apply('voucher.apply', 'JTOOLBAR_APPLY');
      JToolBarHelper::save('voucher.save', 'JTOOLBAR_SAVE');
      JToolBarHelper::save2new('voucher.save');
    }

    JToolBarHelper::cancel('voucher.cancel', 'JTOOLBAR_CLOSE');
  }

}
