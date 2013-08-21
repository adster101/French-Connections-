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

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$title = $this->getUserStateFromRequest($this->context.'.filter.title', 'filter_title', '');
		$this->setState('filter.title', $title);

    $ordering = $this->getUserStateFromRequest($this->context.'.filter.ordering', 'filter_title', '');

    // List state information.
		parent::populateState('id', 'desc');
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

    // Get the unit id - will be present if an owner is looking at reviews for one or other of their units
    $input = JFactory::getApplication()->input;
    $unit_id = $input->get('unit_id','','int');

		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('
      a.id,
      a.unit_id,
      a.property_id,
      a.review_text,
      a.published,
      a.date,
      a.created,
      c.unit_title
    ');

		// From the hello table
		$query->from('#__reviews a');

    
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
    if (!$user->authorise('core.edit','com_review')) { // User not permitted to edit their own reviews
      $query->where('e.created_by = ' . (int) $user->id); // Assume that this is an owner, or a user who we only want to show reviews assigned to properties they own
    }
    
		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
      if ((int) $search ) {
        $query->where('a.property_id = '.(int) $search);

      } else {
        $search = $db->Quote('%'.$db->escape($search, true).'%');
        $query->where('(a.review_text LIKE '.$search.')');
      }
    }

		$listOrdering = $this->getState('list.ordering','a.id');

 		$listDirn = $db->escape($this->getState('list.direction', 'DESC'));
    $query->order($listOrdering,$listDirn);

		return $query;
	}
}