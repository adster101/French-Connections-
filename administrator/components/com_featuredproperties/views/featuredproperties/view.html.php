<?php

/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Invoices.
 */
class FeaturedPropertiesViewFeaturedProperties extends JViewLegacy {

  public function display($tpl = null) {

    $this->state = $this->get('State');
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      throw new Exception(implode("\n", $errors));
    }

    $canDo = FeaturedPropertiesHelper::getActions();

    FeaturedPropertiesHelper::addSubmenu('featuredproperties');

    // Include the component HTML helpers.
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

    $this->addToolbar($canDo);

    JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', FeaturedPropertiesHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.published'), true)
    );

    $this->sidebar = JHtmlSidebar::render();

    parent::display($tpl);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {

    $document = JFactory::getDocument();

    // Set the title property
    // $this->title = JText::_('COM_ENQUIRIES_ENQUIRIES_MANAGE');
    // Set the document title
    $this->document->setTitle($this->title);

    // Set the component toolbar title
    JToolbarHelper::title($this->title);
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar($canDo) {

    if ($canDo->get('core.create')) {
      JToolbarHelper::addNew('featuredproperty.add', 'JTOOLBAR_NEW');
    }

    if ($canDo->get('core.edit')) {
      JToolBarHelper::editList('featuredproperty.edit', 'JTOOLBAR_EDIT');
    }

    if ($canDo->get('core.edit.state')) {
      JToolBarHelper::publish('featuredproperties.publish', 'COM_FEATUREDPROPERTY_MARK_AS_PAID', true);
      JToolBarHelper::unpublish('featuredproperties.unpublish', 'COM_FEATUREDPROPERTY_MARK_AS_UNPAID', true);
      JToolBarHelper::trash('featuredproperties.trash');
    }

    if ($canDo->get('core.delete')) {
      JToolBarHelper::deleteList('Are you sure?', 'featuredproperties.delete', 'JTOOLBAR_DELETE');
    }

    if ($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_featuredproperties');
    }

    // Set the title which appears on the toolbar
    JToolBarHelper::title(JText::_('COM_FEATUREDPROPERTIES_MANAGE_FEATUREDPROPERTIES'));
  }

}