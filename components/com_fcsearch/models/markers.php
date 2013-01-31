<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Suggestions model class for the Finder package.
 *
 * @package     Joomla.Site
 * @subpackage  com_finder
 * @since       2.5
 */
class FcSearchModelMarkers extends JModelList
{
	/**
	 * Context string for the model type.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $context = 'com_fcsearch.markers';

	/**
	 * Method to get an array of data items.
	 *
	 * @return  array  An array of data items.
	 *
	 * @since   2.5
	 */
	public function getItems()
	{
		// Get the items.
		$items = parent::getItems();

		// Convert them to a simple array.
		foreach ($items as $k => $v)
		{
			$items[$k] = $v->title;
		}
    
		return $items;
	}

	/**
	 * Method to build a database query to load the list data.
	 *
	 * @return  JDatabaseQuery  A database query
	 *
	 * @since   2.5
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select required fields
		$query->select('t.title');
		$query->from($db->quoteName('#__classifications') . ' AS t');
		$query->where('t.title LIKE ' . $db->quote('%'.$db->escape($this->getState('input'), true) . '%'));
		//$query->where('t.common = 0');
		//$query->order('t.links DESC');
		//$query->order('t.weight DESC');

    return $query;
	}

	/**
	 * Method to get a store id based on model the configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id. [optional]
	 *
	 * @return  string  A store id.
	 *
	 * @since   2.5
	 */
	protected function getStoreId($id = '')
	{
		// Add the search query state.
		$id .= ':' . $this->getState('input');
		$id .= ':' . $this->getState('language');

		// Add the list state.
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');

		return parent::getStoreId($id);
	}

  /**
   * Method to get the results of the query.
   *
   * @return  array  An array of objects.
   *
   * @since   2.5
   * @throws  Exception on database error.
   */
  public function getResults() {

    // Get the date
    $date = JFactory::getDate();

    // Get the store id.
    $store = $this->getStoreId('getResults');

    // Use the cached data if possible.
    if ($this->retrieve($store)) {
      return $this->retrieve($store);
    }

    // First off we need to get the classification detail
    // E.g. is this a department, area or town etc
    // Create the query to get the search results.
    // Make this a call to get the crumbs trail?
    // Reuse the classification table instance
    $db = $this->getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName('id') . ', ' . $db->quoteName('level') . ',latitude, longitude');
    $query->from($db->quoteName('#__classifications'));
    $query->where($db->quoteName('alias') . ' = ' . $db->quote($this->getState('list.searchterm', '')));

    // Load the result (should only be one) from the database.
    $db->setQuery($query);

    try {
      $row = $db->loadRow();
    } catch (Exception $e) {
      // Log any exception
    }

    // No results found, return an empty array
    if (empty($row)) {
      return array();
    } else {
      $this->location = $row[0];
      $this->level = $row[1];
      $this->latitude = $row[2];
      $this->longitude = $row[3];
    }

    // Proceed and get all the properties in this location
    // TO DO - ensure this works in French as well
    $query->clear();

    $query = $db->getQuery(true);
    $query->select('
              h.id,
              h.parent_id,
              h.level,
              h.title as property_title,
              h.area,
              h.region,
              h.department,
              h.latitude,
              h.longitude,
              h.city,
              h.thumbnail,
              h.occupancy,
              h.swimming,
              (single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms) as bedrooms,
              c.title as location_title,
              b.title as property_type,
              d.title as accommodation_type,
              (
                select 
                  min(tariff) 
                from 
                  qitz3_tariffs 
                where 
                  id = h.id
              ) as from_rate,
              e.title as tariff_based_on,
              f.title as base_currency,
              (
                select 
                  count(*)
                from 
                  qitz3_reviews
                where 
                  property_id = h.id
                group by h.id
              ) as review_count
    ');
    
    if ($this->level == 4) {
      // Add the distance based bit in as this is a town/city search
      $query->select('
        ( 3959 * acos(cos(radians(' . $this->longitude . ')) * 
          cos(radians(h.latitude)) * 
          cos(radians(h.longitude) - radians(' . $this->latitude . '))
          + sin(radians(' . $this->longitude . ')) 
          * sin(radians(h.latitude)))) AS distance
        ');

      $query->join('left', '#__classifications c on c.id = h.city');

      $query->from('#__helloworld h');
    } else {
      $query->from('#__classifications c');
    }
    
    if ($this->level == 1) { // Area level
      $query->join('left', '#__helloworld h on c.id = h.area');
    } else if ($this->level == 2) { // Region level
      $query->join('left', '#__helloworld h on c.id = h.region');
    } else if ($this->level == 3) { // Department level 
      $query->join('left', '#__helloworld h on c.id = h.department');
    }

    $query->join('left', '#__attributes b ON b.id = h.property_type');
    $query->join('left', '#__attributes d ON d.id = h.accommodation_type');
    $query->join('left', '#__attributes e ON e.id = h.tariff_based_on');
    $query->join('left', '#__attributes f ON f.id = h.base_currency');
    $query->join('left', '#__classifications g ON g.id = h.city');

    if ($this->getState('list.start_date')) {
      $query->join('left', '#__availability a on h.id = a.id');
      $query->where('a.start_date <= ' . $db->quote($this->getState('list.start_date', '')));
      $query->where('a.end_date >= ' . $db->quote($this->getState('list.end_date', '')));

      $query->where('a.availability = 1');
    }

    if ($this->level != 4) {
      $query->where('c.id = ' . $this->location);
    }

    if ($this->getState('list.bedrooms')) {
      $query->where('( single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms ) = ' . $this->getState('list.bedrooms', ''));
    }

    if ($this->getState('list.occupancy')) {
      $query->where('occupancy >= ' . $this->getState('list.occupancy', ''));
    }


    // Make sure we only get live properties...
    $query->where('h.expiry_date >= ' . $db->quote($date->toSql()));

    $query->where('h.id !=1');
    // Load the results from the database.
    $db->setQuery($query);
    $rows = $db->loadObjectList();

    // Process results into 
    // Push the results into cache.
    $this->store($store, $rows);

    // Return the results.
    return $this->retrieve($store);
  }  
  
  /**
   * Method to store data in cache.
   *
   * @param   string   $id          The cache store id.
   * @param   mixed    $data        The data to cache.
   * @param   boolean  $persistent  Flag to enable the use of external cache. [optional]
   *
   * @return  boolean  True on success, false on failure.
   *
   * @since   2.5
   */
  protected function store($id, $data, $persistent = true) {
    // Store the data in internal cache.
    $this->cache[$id] = $data;

    // Store the data in external cache if data is persistent.
    if ($persistent) {
      return JFactory::getCache($this->context, 'output')->store(serialize($data), $id);
    }

    return true;
  }

  /**
   * Method to retrieve data from cache.
   *
   * @param   string   $id          The cache store id.
   * @param   boolean  $persistent  Flag to enable the use of external cache. [optional]
   *
   * @return  mixed  The cached data if found, null otherwise.
   *
   * @since   2.5
   */
  protected function retrieve($id, $persistent = true) {
    $data = null;

    // Use the internal cache if possible.
    if (isset($this->cache[$id])) {
      return $this->cache[$id];
    }

    // Use the external cache if data is persistent.
    if ($persistent) {
      $data = JFactory::getCache($this->context, 'output')->get($id);
      $data = $data ? unserialize($data) : null;
    }

    // Store the data in internal cache.
    if ($data) {
      $this->cache[$id] = $data;
    }

    return $data;
  }  
  
  
  

  /**
   * Method to auto-populate the model state.  Calling getState in this method will result in recursion.
   *
   * @param   string  $ordering   An optional ordering field. [optional]
   * @param   string  $direction  An optional direction. [optional]
   *
   * @return  void
   *
   * @since   2.5
   */
  protected function populateState($ordering = null, $direction = null) {
    // Get the configuration options.
    $app = JFactory::getApplication();
    $input = $app->input;
    $params = $app->getParams();
    $user = JFactory::getUser();
    $filter = JFilterInput::getInstance();
    $this->setState('filter.language', $app->getLanguageFilter());
    $request = $input->request;

    // Get each of the possible URL params
    // Get the query string.
    $q = !is_null($request->get('q')) ? $request->get('q', '', 'string') : $params->get('q');
    $q = $filter->clean($q, 'string');

    // Set the search term to the state, this will remember the search term (destination) the user is searching on
    $this->setState('list.searchterm', $q, 'string');

    // Bedrooms search options
    $this->setState('list.bedrooms', $input->get('bedrooms', '', 'int'));
    $app->setUserState('list.bedrooms', $input->get('bedrooms', '', 'int'));

    // Occupancy
    $this->setState('list.occupancy', $input->get('occupancy', '', 'int'));
    $app->setUserState('list.occupancy', $input->get('occupancy', '', 'int'));

    // Start date
    $this->setState('list.start_date', $input->get('start_date', '', 'date'));
    // Store in the session
    $app->setUserState('list.start_date', $input->get('start_date', '', 'date'));

    // End date
    $this->setState('list.end_date', $input->get('end_date', '', 'date'));
    // Store in the session 
    $app->setUserState('list.end_date', $input->get('end_date', '', 'date'));

    // Set the match limit.
    $this->setState('match.limit', 500);

    // Load the user state.
    $this->setState('user.id', (int) $user->get('id'));
    $this->setState('user.groups', $user->getAuthorisedViewLevels());
  }
}
