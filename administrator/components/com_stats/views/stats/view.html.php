<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class StatsViewStats extends JViewLegacy {

  
  function display($tpl = null) {    // Gets the info from the model and displays the template 

    
    $this->item->id = 13;
    
    $this->state = $this->get('State');
    $this->data = $this->get('GraphData');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

    $this->setDocument();

    $this->addToolBar();

    parent::display($tpl);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_STATS_VIEW_STATS'));
		$document->addScript('https://www.google.com/jsapi',false,false);
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
    JToolbarHelper::help('','');
    // Set the title which appears on the toolbar
    JToolBarHelper::title(JText::_('COM_STATS_VIEW_STATS'));
  }
}
