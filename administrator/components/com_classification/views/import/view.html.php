<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class ClassificationViewImport extends JViewLegacy {

  /**
   *  view display method
   * @return void
   */
  function display($tpl = null) {
   
   
    // Get the view 
    $view = strtolower(JRequest::getVar('view'));

    // Load the submenu
    ClassificationHelper::addSubmenu($view);
    
    
    $document = JFactory::getDocument();
 		$document->setTitle(JText::_('Import Locations'));
    JToolBarHelper::title(JText::_('Import Locations'));
		JToolBarHelper::apply('classification.import', 'JTOOLBAR_APPLY');
		JToolBarHelper::cancel('classification.cancel', 'JTOOLBAR_CLOSE');

    
    // Add the side bar
		$this->sidebar = JHtmlSidebar::render();
    
    // Set the doscument
    
    // Display the template
    parent::display($tpl);
    
    
  }
  
}

  