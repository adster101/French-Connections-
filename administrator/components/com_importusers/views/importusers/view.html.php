<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class ImportUsersViewImportUsers extends JViewLegacy {

  /**
   *  view display method
   * @return void
   */
  function display($tpl = null) {
   
   
    // Get the view 
    $view = strtolower(JRequest::getVar('view'));

    $this->form = $this->get('Form');
    
    $document = JFactory::getDocument();
 		$document->setTitle(JText::_('Import Locations'));

    JToolBarHelper::title(JText::_('Import Locations'));
		JToolBarHelper::apply('importusers.import', 'JTOOLBAR_APPLY');
		JToolBarHelper::cancel('importusers.cancel', 'JTOOLBAR_CLOSE');    
    // Display the template
    parent::display($tpl);
    
    
  }
  
}

  