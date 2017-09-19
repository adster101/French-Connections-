<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class ImportViewProperty_listings extends JViewLegacy {

  /**
   *  view display method
   * @return void
   */
  function display($tpl = null) {
   
   
    // Get the view 
    $view = strtolower(JRequest::getVar('view'));

    // Load the submenu
    ImportHelper::addSubmenu($view);
    // Add the side bar
		$this->sidebar = JHtmlSidebar::render();  
  
    $this->form = $this->get('Form');
    
    $document = JFactory::getDocument();
 		$document->setTitle(JText::_('Import property listings'));

    JToolBarHelper::title(JText::_('Import property listings'));
		JToolBarHelper::apply('property_listings.import', 'JTOOLBAR_APPLY');
    // Display the template
    parent::display($tpl);
    
    
  }
  
}

  