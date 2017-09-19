<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class FcadminViewFcAdmin extends JViewLegacy
{

  /**
   *  view display method
   * @return void
   */
  function display($tpl = null)
  {

    $document = JFactory::getDocument();
    $document->setTitle(JText::_('Import FC data'));

    // Get the view 
    $view = strtolower(JRequest::getVar('view'));

    // Load the submenu
    FcadminHelper::addSubmenu($view);

    JToolBarHelper::title(JText::_('COM_FCADMIN_TITLE'));

    // Add the side bar
    $this->sidebar = JHtmlSidebar::render();

    // Add the toolbar
    $this->addToolbar();  
    
    // Display the template
    parent::display($tpl);
  }

  protected function addToolbar()
  {
    $user = JFactory::getUser();

    if ($user->authorise('core.admin', 'com_fcadmin'))
    {
      JToolBarHelper::preferences('com_fcadmin');
    }
  }

}

