<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class SpecialOffersViewUnits extends JViewLegacy
{

  protected $items;
  protected $state;
  protected $pagination;

  function display($tpl = null)
  {

    // Get an instance of the Listing model
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');
    $this->setModel(JModelLegacy::getInstance('Units', 'RentalModel'), true);
    
    // Get data from the model
    $this->items = $this->get('Items');
    $this->state = $this->get('State');
    $this->pagination = $this->get('Pagination');
    $this->filterForm = $this->get('FilterForm');
    $this->activeFilters = $this->get('ActiveFilters');
    

    parent::display($tpl);
  }


 
  /**
   * Returns an array of fields the special offers can be filtered on based on their date status
   *
   * @return  array  Array containing the field name to sort by as the key and display text as value
   *
   * @since   3.0
   */
  protected function getFilterFields()
  {
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
  protected function getSortFields()
  {
    return array(
        'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
        'a.title' => JText::_('JGLOBAL_TITLE'),
        'a.id' => JText::_('JGRID_HEADING_ID')
    );
  }

}
