<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_fcfinder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Search model class to perform accommodation searches through the FC properties.
 *
 * @package     Joomla.Site
 * @subpackage  com_fcfinder
 * @since       3.0
 */
class FcSearchModelSearch extends JModelList
{
	/**
	 * Context string for the model type
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $context = 'com_fcsearch.search';  
  
	/**
	 * The location integer is the classification id
	 *
	 * @var   query
	 * @since  2.5
	 */
	protected $location;  
  
	/**
	 * Method to get the results of the query.
	 *
	 * @return  array  An array of objects.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function getResults()
	{
		

		// Get the store id.
		$store = $this->getStoreId('getResults');
    

		// Use the cached data if possible.
		if ($this->retrieve($store))
		{
			return $this->retrieve($store);
		}

		// First off we need to get the classification detail
    // E.g. is this a department, area or town etc
    

		// Create the query to get the search results.
    // Make this a call to get the crumbs trail?
    // Reuse the classification table instance
		$db = $this->getDbo();
		$query = $db->getQuery(true);
    $query->select($db->quoteName('id') . ', ' . $db->quoteName('level'));
    $query->from($db->quoteName('#__classifications'));
    $query->where($db->quoteName('alias') . ' = ' . $db->quote($this->getState('list.searchterm',''))) ;
    
    // Load the result (should only be one) from the database.
		$db->setQuery($query);
		try {
    $row = $db->loadRow();
    } catch (Exception $e) {
      print_r($e);die;
    }
    
    // No results found, return an empty array
    if (empty($row)) {
      return array();
    } else {
      $this->location = $rows[0];
    }
    
    // Proceed and get all the properties in this location
    // TO DO - ensure this works in French as well
    $query->clear();
    $query = $db->getQuery(true);
    $query->select(
              $db->quoteName('h.id')
              $db->quoteName('h.id')
              $db->quoteName('h.id')
              $db->quoteName('h.id')
              $db->quoteName('h.id')
              $db->quoteName('h.id')
              $db->quoteName('h.id')
              $db->quoteName('h.id')
              $db->quoteName('h.id')
              $db->quoteName('h.id')
              $db->quoteName('h.id')
              $db->quoteName('h.id')
           
            )
    
    
	

		// Load the results from the database.
		$db->setQuery($query);
		$rows = $db->loadObjectList('link_id');

		// Set up our results container.
		$results = $items;

		// Convert the rows to result objects.
		foreach ($rows as $rk => $row)
		{
			// Build the result object.
			$result = unserialize($row->object);
			$result->weight = $results[$rk];
			$result->link_id = $rk;

			// Add the result back to the stack.
			$results[$rk] = $result;
		}

		// Switch to a non-associative array.
		$results = array_values($results);

		// Push the results into cache.
		$this->store($store, $results);

		// Return the results.
		return $this->retrieve($store);
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
	protected function populateState($ordering = null, $direction = null)
	{
		// Get the configuration options.
		$app = JFactory::getApplication();
		$input = $app->input;
		$params = $app->getParams();
		$user = JFactory::getUser();
		$filter = JFilterInput::getInstance();

		$this->setState('filter.language', $app->getLanguageFilter());  
		$request = $input->request;
		$options = array();

    // Get the query string.
		$q = !is_null($request->get('q')) ? $request->get('q', '', 'string') : $params->get('q');
		$q = $filter->clean($q, 'string');
    
    // Set the search term to the state, this will remember the search term (destination) the user is searching on
    $this->setState('list.searchterm', $q, 'string');
    
    // Load the list state.
    // Will come from the search results page.
		$this->setState('list.start', $input->get('limitstart', 0, 'uint'));
		$this->setState('list.limit', $input->get('limit', $app->getCfg('list_limit', 20), 'uint'));

		// Load the sort direction.
		$dirn = $params->get('sort_direction', 'asc');
		switch ($dirn)
		{
			case 'asc':
				$this->setState('list.direction', 'ASC');
				break;

			default:
			case 'desc':
				$this->setState('list.direction', 'DESC');
				break;
		}

		// Set the match limit.
		$this->setState('match.limit', 1000);

		// Load the parameters.
		$this->setState('params', $params);

		// Load the user state.
		$this->setState('user.id', (int) $user->get('id'));
		$this->setState('user.groups', $user->getAuthorisedViewLevels());
    
  }  
  
	/**
	 * Method to get a store id based on model the configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string   $id    An identifier string to generate the store id. [optional]
	 * @param   boolean  $page  True to store the data paged, false to store all data. [optional]
	 *
	 * @return  string  A store id.
	 *
	 * @since   2.5
	 */
	protected function getStoreId($id = '', $page = true)
	{
		// Default will generate store IDs based on start (i.e. page number), limit (i.e. results per page), ordering
    // Possible additional things to cache against would be
    // language
    // dates
    // prices
    // facilities
    // occupancy
    // bedrooms
    // and so on and son on
	  if ($page)
		{
			// Add the list state for page specific data.
			$id .= ':' . $this->getState('list.start');
			$id .= ':' . $this->getState('list.limit');
			$id .= ':' . $this->getState('list.direction');
			$id .= ':' . $this->getState('list.searchterm');
    }
		return parent::getStoreId($id);
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
	protected function retrieve($id, $persistent = true)
	{
		$data = null;

		// Use the external cache if data is persistent.
		if ($persistent)
		{
			$data = JFactory::getCache($this->context, 'output')->get($id);
			$data = $data ? unserialize($data) : null;
		}

		// Store the data in internal cache.
		if ($data)
		{
			$this->cache[$id] = $data;
		}    
    return $data;
  }  
  
}