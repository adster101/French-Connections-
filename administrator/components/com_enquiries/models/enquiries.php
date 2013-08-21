<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */

class EnquiriesModelEnquiries extends JModelList
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
				'id', 'e.id',
				'state', 'e.state',
				'created', 'e.date_created',
        'title','hw.title',
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
		parent::populateState('e.id','desc');
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
      e.id,
      e.forename,
      e.surname,
      e.email,
      e.message,
      e.start_date,
      e.end_date,
      e.date_created,
      e.state,
      e.property_id,
      e.adults,
      e.children,
      e.replied,
      e.date_replied
    ');

		// From the hello table
		$query->from('#__enquiries e');

    $query->leftJoin('#__property p on p.id = e.property_id');

    // Filter by published state
		$published = $this->getState('filter.published');

    if (is_numeric($published)) {
			$query->where('e.state = ' . (int) $published);
		} else {
			$query->where('e.state IN (0,1)');
    }

    // Need to ensure that owners only see reviews assigned to their properties
    if (!$user->authorise('core.edit','com_enquiries') && $user->authorise('core.edit.own', 'com_enquiries')) { // User not permitted to edit their enquiries globally
      $query->where('p.created_by = ' . (int) $user->id); // Assume that this is an owner, or a user who we only want to show reviews assigned to properties they own
    }

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
      if ((int) $search ) {
        $query->where('e.property_id = '.(int) $search);

      } else {
        $search = $db->Quote('%'.$db->escape($search, true).'%');
        $query->where('(e.message LIKE '.$search.')');
      }
    }

    $listOrdering = $this->getState('list.ordering','date_created');
		$listDirn = $db->escape($this->getState('list.direction', 'desc'));
    $query->order($db->escape($listOrdering).' '.$listDirn);

    return $query;
	}
}

