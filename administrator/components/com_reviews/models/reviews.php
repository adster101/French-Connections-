<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */

class ReviewsModelReviews extends JModelList
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

    // List state information.
		parent::populateState();
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
    
		$query->select('
      r.id,
      r.property_id,
      r.title,
      r.review_text,
      r.published,
      r.date
    ');
		
		// From the hello table
		$query->from('#__reviews r');
    
    // Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('r.published = ' . (int) $published);
		}
		elseif ($published === '') {
			$query->where('(r.published = 0 OR r.published = 1)');
		} 
    
		// Filter by search in title
		// TODO - Try and tidy up this logic a bit.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
      $search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(r.review_text LIKE '.$search.')');
    }
    
    
		$listOrdering = $this->getState('list.ordering','id');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));


		return $query;
	}   
}