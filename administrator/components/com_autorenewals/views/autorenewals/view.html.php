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
class AutorenewalsViewAutorenewals extends JViewLegacy {

  public function display($tpl = null) {

    $this->state = $this->get('State');
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');
    $this->filterForm = $this->get('FilterForm');


    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      throw new Exception(implode("\n", $errors));
    }

    $canDo = AutoRenewalsHelper::getActions();

    AutoRenewalsHelper::addSubmenu('autorenewals');

    $this->addToolbar($canDo);


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

    // Set the title property
    // $this->title = JText::_('COM_ENQUIRIES_ENQUIRIES_MANAGE');
    // Set the document title
    $this->document->setTitle($this->title);

    // Set the component toolbar title
    JToolbarHelper::title($this->title);
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar($canDo) {

    if ($canDo->get('core.edit.state')) {
      JToolBarHelper::trash('autorenewals.trash');
    }
    
    if ($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_autorenewals');
    }

    // Set the title which appears on the toolbar
    JToolBarHelper::title(JText::_('COM_AUTORENEWALS'));
  }

}