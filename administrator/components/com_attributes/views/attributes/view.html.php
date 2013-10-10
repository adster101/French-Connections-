<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class AttributesViewAttributes extends JViewLegacy {

	protected $items;
	protected $state;  
 	protected $attributes;
 	protected $pagination;
 

  function display($tpl = null) {    // Gets the info from the model and displays the template 
    // Get data from the model
    $this->items = $this->get('Items');
    $this->state = $this->get('State');
		$this->pagination	= $this->get('Pagination');

    $this->attributes = $this->getAttributeTypes();
    $this->setDocument();
    
    $view = strtolower(JRequest::getVar('view'));

    AttributesHelper::addSubmenu($view);

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

    JToolBarHelper::addNew('attribute.add', 'JTOOLBAR_NEW');
    JToolBarHelper::editList('attribute.edit', 'JTOOLBAR_EDIT');
    JToolBarHelper::publish('attributes.publish', 'JTOOLBAR_PUBLISH', true);
    JToolBarHelper::unpublish('attributes.unpublish', 'JTOOLBAR_UNPUBLISH', true);
    JToolBarHelper::trash('attributes.trash');
    JToolBarHelper::deleteList('Are you sure?', 'attributes.delete', 'JTOOLBAR_DELETE');
    // Set the title which appears on the toolbar 
    JToolBarHelper::title(JText::_('Manage Property Attributes'));
    
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_attribute_type_id',
			JHtml::_('select.options', $this->attributes, 'value', 'text', $this->state->get('filter.attribute_type_id'))
		);
    
  }

  /*
   * Method to get a list of property attribute types
   * 
   * 
   * 
   */
  public function getAttributeTypes()
  {
 		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select some fields
		$query->select('id as value, title as text');     
    $query->from('#__attributes_type');   
    $query->order('title asc');
    $db->setQuery($query);
    $items = $db->loadObjectList();
    
    return $items;
    
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
