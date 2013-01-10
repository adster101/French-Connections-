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
				'id', 'so.id',
				'title', 'so.title'		,	
				'state', 'so.state',
				'created', 'so.date_created',
        'title','hw.title',
				'publish_up', 'so.publish_up',
				'publish_down', 'so.publish_down',
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

    // Get the user to authorise
    $user	= JFactory::getUser();

		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select some fields
		$query->select('
      so.id,
      so.published,
      so.property_id,
      so.start_date,
      so.end_date,
      so.date_created,
      so.title,
      so.description,
      so.status,
      so.approved_by,
      so.approved_date,
      hw.title as property_title
    ');
		
		// From the hello table
		$query->from('#__special_offers so');
    
    $query->leftJoin('#__helloworld hw on hw.id = so.property_id');
    
    // Filter by published state
		$published = $this->getState('filter.published');

    if (is_numeric($published)) {
			$query->where('so.published = ' . (int) $published);
		} else {
			$query->where('so.published IN (0,1)');
    }
    
    // Need to ensure that owners only see reviews assigned to their properties
    if (!$user->authorise('core.edit.own','com_review')) { // User not permitted to edit their own reviews
      $query->where('hw.created_by = ' . (int) $user->id); // Assume that this is an owner, or a user who we only want to show reviews assigned to properties they own
    } 
        
		// Filter by search in title
		$search = $this->getState('filter.search');
    
		if (!empty($search)) {
      if ((int) $search ) {
        $query->where('so.property_id = '.(int) $search);

      } else {
        $search = $db->Quote('%'.$db->escape($search, true).'%');
        $query->where('(so.review_text LIKE '.$search.')');
      }
    }
    
    $listOrdering = $this->getState('list.ordering','id');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));  
    $query->order($db->escape($listOrdering).' '.$listDirn);
		
    return $query;
	}  
}