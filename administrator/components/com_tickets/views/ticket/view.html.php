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
class TicketsViewTicket extends JViewLegacy {

  protected $item;
  protected $form;
  protected $state;

  /**
   * Display the view
   */
  public function display($tpl = null) {

    $app = JFactory::getApplication();


    $this->form = $this->get('Form');
    
    $this->state = $this->get('State');

    $this->item = $this->get('Item');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      throw new Exception(implode("\n", $errors));
    }

    $canDo = TicketsHelper::getActions();

    //TicketsHelper::addSubmenu('tickets');
    //$this->sidebar = JHtmlSidebar::render();


    $this->addToolbar($canDo);


    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar.
   *
   * @since	1.6
   */
  protected function addToolbar($canDo) {
    
		$isNew		= ($this->item->id == 0);

    $title = ($isNew) ? JText::_('COM_TICKETS_TICKET_NEW') : JText::sprintf('COM_TICKETS_TICKET_TITLE', $this->item->title, $this->item->id);
    
    JToolBarHelper::title($title);

    if ($canDo->get('core.create')) {
      JToolBarHelper::apply('ticket.apply', 'JTOOLBAR_APPLY');
      JToolBarHelper::save('ticket.save', 'JTOOLBAR_SAVE');
      JToolBarHelper::save2new('ticket.save');
    }

    JToolBarHelper::cancel('ticket.cancel', 'JTOOLBAR_CLOSE');
  }

}
