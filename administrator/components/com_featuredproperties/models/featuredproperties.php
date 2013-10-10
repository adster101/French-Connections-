<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */

class FeaturedPropertiesModelFeaturedProperties extends JModelList
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
				'state', 'a.state',
				'start_date', 'a.start_date',
        'end_date', 'a.end_date',
        'featured_property_type'
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
    
    $type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type','');
    $this->setState('filter.type', $type);

    // List state information.
		parent::populateState('a.start_date','asc');
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
      a.date_updated,
      a.property_id,
      date_format(a.start_date, "%d/%m/%Y") as start_date,
      date_format(a.end_date, "%d/%m/%Y") as end_date,
      a.notes,
      a.published,
      b.title
    ');

		// From the hello table
		$query->from('#__featured_properties a');
    
    // Join the category 
    $query->join('left', '#__categories b on b.id = a.featured_property_type');

    // Filter by published state
		$published = $this->getState('filter.published');

    if (is_numeric($published)) {
			$query->where('a.published = ' . (int) $published);
		} else {
			$query->where('a.published IN (0,1)');
    }

    $query->where('a.end_date >= ' . $db->quote(JFactory::getDate()));
    
		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
      if ((int) $search ) {
        $query->where('a.property_id = '.(int) $search);

      } else {
        $search = $db->Quote('%'.$db->escape($search, true).'%');
        $query->where('(a.notes LIKE '.$search.')');
      }
    }
    
    $type = $this->getState('filter.type');
    
    if (!empty($type)){
      $query->where('a.featured_property_type = ' . (int) $type);
    }

    $listOrdering = $this->getState('list.ordering','date_created');
		$listDirn = $db->escape($this->getState('list.direction', 'desc'));
    $query->order($db->escape($listOrdering).' '.$listDirn);

    return $query;
	}
}

