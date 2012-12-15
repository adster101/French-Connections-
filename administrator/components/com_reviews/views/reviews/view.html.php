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
    // Get data from the model
    $this->items = $this->get('Items');
    
    $this->state = $this->get('State');
		$this->pagination	= $this->get('Pagination');

    $this->setDocument();
    
    $view = strtolower(JRequest::getVar('view'));

    ReviewsHelper::addSubmenu($view);

    $this->addToolBar();
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
    $document->setTitle(JText::_('Manage Property Attributes'));
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
    $document = JFactory::getDocument();

    JToolBarHelper::addNew('review.add', 'JTOOLBAR_NEW');
    JToolBarHelper::editList('review.edit', 'JTOOLBAR_EDIT');
    JToolBarHelper::publish('reviews.publish', 'JTOOLBAR_PUBLISH', true);
    JToolBarHelper::unpublish('reviews.unpublish', 'JTOOLBAR_UNPUBLISH', true);
    JToolBarHelper::trash('reviews.trash');
    JToolBarHelper::deleteList('Are you sure?', 'reviews.delete', 'JTOOLBAR_DELETE');
    // Set the title which appears on the toolbar 
    JToolBarHelper::title(JText::_('Manage reviews'));
 
    JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);
		    
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
