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
class FcadminViewmyobsync extends JViewLegacy
{

  protected $form;

  /**
   * Display the view
   */
  public function display($tpl = null)
  {

    $view = strtolower(JRequest::getVar('view'));

    // Load the submenu
    //FcadminHelper::addSubmenu($view);
    
    // Render the submenu
    //$this->sidebar = JHtmlSidebar::render();

    JToolBarHelper::title(JText::_('COM_FCADMIN_IMPORT_INVOICES_TITLE'));
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

    $bar = JToolbar::getInstance('myob');

    // Add a batch button
    if ($user->authorise('core.create', 'com_invoices') && $user->authorise('core.edit', 'com_invoices') && $user->authorise('core.edit.state', 'com_invoices'))
    {
      $bar->appendButton('Popup', 'upload', 'COM_FCADMIN_UPLOAD_INVOICES', 'index.php?option=com_fcadmin&view=invoices&tmpl=component', 800, 300);
    }

    // Add the download card file link
    $bar->appendButton('Link', 'download', 'COM_FCADMIN_DOWNLOAD_USER_CARDS', '/administrator/index.php?option=com_fcadmin&task=downloads.usercards&format=raw');

    // Add the download job file link
    $bar->appendButton('Link', 'download', 'COM_FCADMIN_DOWNLOAD_JOB_FILE', 'index.php?option=com_fcadmin&task=downloads.jobfiles&format=raw');

    // Add a back button for usability...
    JToolbarHelper::back();

    if ($user->authorise('core.admin', 'com_fcadmin'))
    {
      JToolBarHelper::preferences('com_fcadmin');
    }
  }

}
