<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class StatsViewStats extends JViewLegacy
{

  function display($tpl = null)
  {    // Gets the info from the model and displays the template 
    $this->state = $this->get('State');
    $this->data = $this->get('GraphData');
    $this->filterForm = $this->get('FilterForm');
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
  protected function setDocument()
  {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_STATS_VIEW_STATS'));
    //$document->addScript(JURI::root() . "/media/fc/js/general.js", 'text/javascript', true);
    $document->addScript(JURI::root() . "/media/fc/js/jquery-ui-1.8.23.custom.min.js", 'text/javascript', true);
    $document->addScript(JURI::root() . "/media/fc/js/date-range.js", 'text/javascript', true);
    $document->addStyleSheet(JURI::root() . "/media/fc/css/jquery-ui-1.8.23.custom.css", 'text/css', "screen");
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar()
  {
    // JToolbarHelper::help('', '');
    // Set the title which appears on the toolbar
    JToolBarHelper::title(JText::_('COM_STATS_VIEW_STATS'));
  }

}
