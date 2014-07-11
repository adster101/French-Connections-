<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class ImportViewImport extends JViewLegacy {

  /**
   *  view display method
   * @return void
   */
  function display($tpl = null) {
       
    $document = JFactory::getDocument();
 		$document->setTitle(JText::_('Import FC data'));
  
    // Get the view 
    $view = strtolower(JRequest::getVar('view'));

    // Load the submenu
    ImportHelper::addSubmenu($view);
    
    JToolBarHelper::title(JText::_('Import'));
    
    // Add the side bar
		$this->sidebar = JHtmlSidebar::render();
    
    // Display the template
    parent::display($tpl);
  }
}

  