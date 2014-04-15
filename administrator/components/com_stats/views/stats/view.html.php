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

    // Get data from the model
    $this->items = $this->get('Items');

    $this->state = $this->get('State');

    // Get an instance of the property model
    $this->data = $this->get('GraphData');

    $this->setDocument();

    $view = strtolower(JRequest::getVar('view'));


    $this->addToolBar();

    parent::display($tpl);
  }

  /**
   * Adds the submenu details for this view
   */
  protected function addSubMenu() {

   

    RentalHelper::addSubmenu('stats');

    $this->sidebar = JHtmlSidebar::render();
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

  /**
   * Returns an array of fields the special offers can be filtered on based on their date status
   *
   * @return  array  Array containing the field name to sort by as the key and display text as value
   *
   * @since   3.0
   */
  protected function getFilterFields() {
    $options = array();
    $options[] = JHtml::_('select.option', '1', 'COM_SPECIALOFFERS_OFFER_STATUS_EXPIRED');
    $options[] = JHtml::_('select.option', '2', 'COM_SPECIALOFFERS_OFFER_STATUS_ACTIVE');
    $options[] = JHtml::_('select.option', '3', 'COM_SPECIALOFFERS_OFFER_STATUS_SCHEDULED');
    $options[] = JHtml::_('select.option', '4', 'COM_SPECIALOFFERS_OFFER_STATUS_AWAITING_APPROVAL');
    return $options;
  }

  /**
   * Returns an array of fields the table can be sorted by
   *
   * @return  array  Array containing the field name to sort by as the key and display text as value
   *
   * @since   3.0
   */
  protected function getSortFields() {
    return array(
        'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
        'a.title' => JText::_('JGLOBAL_TITLE'),
        'a.id' => JText::_('JGRID_HEADING_ID')
    );
  }

}
