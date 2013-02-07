<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */

class AttributesModelAttributes extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title'		,	
				'state', 'a.state',
				'ordering', 'a.ordering',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
			);
    }
		parent::__construct($config);
	}
  
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$title = $this->getUserStateFromRequest($this->context.'.filter.title', 'filter_title', '');
		$this->setState('filter.title', $title);  

		$attributeTypeId = $this->getUserStateFromRequest($this->context.'.filter.attribute_type_id', 'filter_attribute_type_id');
		$this->setState('filter.attribute_type_id', $attributeTypeId);

    // List state information.
		parent::populateState('a.attribute_type_id', 'asc');
	}

  /**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
   * 
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select some fields
    // TO DO - This needs abstracting into an attributes table instance and reused within the property manager component
    
		$query->select('a.ordering,a.id,a.attribute_type_id,a.title,a.published,a.search_filter,at.title as attribute_type, at.id as attribute_type_id');
    $query->join('left','#__attributes_type at ON a.attribute_type_id = at.id');
		
		// From the hello table
		$query->from('#__attributes a');
    
		$listOrdering = $this->getState('list.ordering','id');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));

    $attributeTypeId = $this->getState('filter.attribute_type_id');
    if (is_numeric($attributeTypeId)) {
      $query->where('attribute_type_id = ' . $attributeTypeId);
    }
    
    $search = $this->getState('filter.search', '');
    
    if ($search) {
      $query->where('a.title like (\'%' . $search . '%\')');
    }
    
		$query->order($db->escape($listOrdering).' '.$listDirn);

		return $query;
	}   
}