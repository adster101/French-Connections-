<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class SpecialOffersViewSpecialOffers extends JViewLegacy
{

  protected $items;
  protected $state;
  protected $pagination;

  function display($tpl = null)
  {

    // Gets the info from the model and displays the template 
    $canDo = SpecialOffersHelper::getActions();

    // Get data from the model
    $this->items = $this->get('Items');
    $this->state = $this->get('State');
    $this->pagination = $this->get('Pagination');
    $this->filterForm = $this->get('FilterForm');
    $this->activeFilters = $this->get('ActiveFilters');

    $this->setDocument();
    //$this->addSubMenu();
    
    $this->addToolBar($canDo);

    parent::display($tpl);
  }

  /**
   * Adds the submenu details for this view
   */
  protected function addSubMenu()
  {
    RentalHelper::addSubmenu('specialoffers');
    $this->sidebar = JHtmlSidebar::render();

  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_(''));
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar($canDo)
  {
    $document = JFactory::getDocument();

    if ($canDo->get('core.create'))
    {
      JToolBarHelper::addNew('specialoffer.add', 'COM_SPECIAL_OFFERS_NEW_OFFER');
    }

    if ($canDo->get('core.edit'))
    {
      JToolBarHelper::editList('specialoffer.edit', 'JTOOLBAR_EDIT');
    }

    if ($canDo->get('core.edit.state'))
    {


      JToolBarHelper::publish('specialoffers.publish', 'JTOOLBAR_PUBLISH', true);
      JToolBarHelper::unpublish('specialoffers.unpublish', 'JTOOLBAR_UNPUBLISH', true);
      JToolBarHelper::trash('specialoffers.trash');
    }

    if ($canDo->get('core.delete'))
    {
      JToolBarHelper::deleteList('Are you sure?', 'specialoffers.delete', 'JTOOLBAR_DELETE');
    }
    else
    {
      JToolBarHelper::custom('specialoffer.canceloffer', 'delete', '', JText::_('COM_SPECIALOFFERS_OFFER_EXPIRE_OFFER'));
    }

    JToolBarHelper::help('COM_SPECIALOFFERS_COMPONENT_HELP_VIEW', true);
    if ($canDo->get('core.admin'))
    {
      JToolBarHelper::preferences('com_specialoffers');
    }
    // Set the title which appears on the toolbar
    JToolBarHelper::title(JText::_('COM_SPECIALOFFERS_MANAGE_OFFERS'));
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
