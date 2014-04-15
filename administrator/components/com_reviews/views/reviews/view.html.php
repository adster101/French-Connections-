<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class ReviewsViewReviews extends JViewLegacy {

  protected $items;
  protected $state;
  protected $pagination;

  function display($tpl = null) {    // Gets the info from the model and displays the template
    $canDo = ReviewsHelper::getActions();

    // Get data from the model
    $this->items = $this->get('Items');

    $this->state = $this->get('State');
    $this->pagination = $this->get('Pagination');

    $this->setDocument();

    $view = strtolower(JRequest::getVar('view'));

    $this->addSubMenu($canDo);
    $this->addToolBar($canDo);

    parent::display($tpl);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_(''));
  }

  /**
   * Adds the submenu details for this view
   */
  protected function addSubMenu($canDo) {

    if ($canDo->get('core.edit.state')) {
      JHtmlSidebar::addFilter(
              JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
      );
    }

    //RentalHelper::addSubmenu('reviews');

    //$this->sidebar = JHtmlSidebar::render();
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar($canDo) {

    if ($canDo->get('core.create')) {
      JToolBarHelper::addNew('review.add', 'JTOOLBAR_NEW');
    }

    if ($canDo->get('core.edit')) {
      JToolBarHelper::editList('review.edit', 'JTOOLBAR_EDIT');
    }

    if ($canDo->get('core.edit.state')) {
      JToolBarHelper::publish('reviews.publish', 'JTOOLBAR_PUBLISH', true);
      JToolBarHelper::unpublish('reviews.unpublish', 'JTOOLBAR_UNPUBLISH', true);
      JToolBarHelper::trash('reviews.trash');
    }

    if ($canDo->get('core.edit.delete')) {
      JToolBarHelper::deleteList('Are you sure?', 'reviews.delete', 'JTOOLBAR_DELETE');
    }

    JToolBarHelper::help('COM_REVIEWS_COMPONENT_HELP_VIEW', true);


    // Set the title which appears on the toolbar
    JToolBarHelper::title(JText::_('COM_REVIEW_VIEW_REVIEWS'));
  }

  /**
   * Returns an array of fields the table can be sorted by
   *
   * @return  array  Array containing the field name to sort by as the key and display text as value
   *
   * @since   3.0
   */
  protected function getSortFields($canDo) {
    return array(
        'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
        'a.title' => JText::_('JGLOBAL_TITLE'),
        'a.id' => JText::_('JGRID_HEADING_ID')
    );
  }

}
