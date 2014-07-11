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
class FcadminViewInvoices extends JViewLegacy
{

  protected $form;

  /**
   * Display the view
   */
  public function display($tpl = null)
  {

    $this->form = $this->get('Form');

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
    
    jimport('frenchconnections.toolbar.button.fcstandard');
    
    $bar = JToolbar::getInstance('myob');

    $bar->appendButton('FcStandard', 'chevron-right', 'COM_FCADMIN_UPLOAD_INVOICES', 'invoices.import', 'btn btn-primary', false);
  }

}
