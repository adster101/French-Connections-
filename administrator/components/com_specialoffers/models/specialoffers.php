<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */

class SpecialOffersModelSpecialOffers extends JModelList
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
				'published', 'a.published',
				'created', 'a.date_created',
        'title','c.unit_title',
				'start_date', 'a.start_date',
				'end_date', 'a.end_date',
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
		    
    // List state information.
		parent::populateState('a.start_date','desc');
	}
 
  /**
	 * Overloaded _getListCount to remove the joins as the resultant query is slow.
   * Returns a record count for the query.
	 *
	 * @param   JDatabaseQuery|string  $query  The query.
	 *
	 * @return  integer  Number of rows for query.
	 *
	 * @since   12.2
	 */
	protected function _getListCount($query)
	{
		// Use fast COUNT(*) on JDatabaseQuery objects if there no GROUP BY or HAVING clause:
		if ($query instanceof JDatabaseQuery
			&& $query->type == 'select'
			&& $query->group === null
			&& $query->having === null)
		{
			$query = clone $query;
			$query->clear('select')->clear('order')->clear('join')->select('COUNT(*)');

			$this->_db->setQuery($query);
			return (int) $this->_db->loadResult();
		}

		// Otherwise fall back to inefficient way of counting all results.
		$this->_db->setQuery($query);
		$this->_db->execute();

		return (int) $this->_db->getNumRows();
	}

  /**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
   * 
	 */
	protected function getListQuery()
	{

    // Get the user to authorise
    $user	= JFactory::getUser();

		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select some fields
		$query->select('
      a.id,
      a.published,
      a.unit_id,
      date_format(a.start_date, "%d-%m-%Y") as start_date,
      date_format(a.end_date, "%d-%m-%Y") as end_date,
      date_format(a.date_created, "%d-%m-%Y") as date_created,
      a.title,
      a.description,
      a.status,
      a.approved_by,
      a.approved_date,
      c.unit_title as unit_title,
      e.id as listing_id
    ');
		
		// From the hello table
		$query->from('#__special_offers a');
    
    $query->join('left', '#__unit b on b.id = a.unit_id');
    

    $query->join('left', '#__unit_versions c on (b.id = c.unit_id and c.id = (select max(d.id) from #__unit_versions d where unit_id = b.id))');
    $query->join('left', '#__property e on e.id = b.property_id');
    
    // Filter by published state
		$published = $this->getState('filter.published');

    if (is_numeric($published)) {
			$query->where('a.published = ' . (int) $published);
		} else {
			$query->where('a.published IN (0,1)');
    }
    
    // Need to ensure that owners only see reviews assigned to their properties
    if (!$user->authorise('core.edit','com_specialoffers')) { // User not permitted to edit their own reviews
      $query->where('e.created_by = ' . (int) $user->id); // Assume that this is an owner, or a user who we only want to show reviews assigned to properties they own
    } 
    
    $state = $this->getState('filter.state',0);
    
    if($state == 1) {
      $query->where('a.end_date < now()');
    } else if ($state == 2) {
      $query->where('a.start_date <= now() and a.end_date >= now()');
    } else if ($state == 3) {
      $query->where('a.start_date > now() and a.published = 1');
    } else if ($state == 4) {
      $query->where('a.published = 0 and a.start_date > now()');
    }
    
		// Filter by search in title
		$search = $this->getState('filter.search');
    
		if (!empty($search)) {
      if ((int) $search ) {
        $query->where('a.property_id = '.(int) $search);

      } else {
        $search = $db->Quote('%'.$db->escape($search, true).'%');
        $query->where('(a.description LIKE '.$search.')');
      }
    }
    
    $listOrdering = $this->getState('list.ordering','date_created');
		$listDirn = $db->escape($this->getState('list.direction', 'desc'));  
    $query->order($db->escape($listOrdering).' '.$listDirn);
		
    return $query;
	}  
}