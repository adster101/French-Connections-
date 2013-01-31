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
class FcSearchModelSearch extends JModelList {

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

  /*
   * The 'level' of the search. 1-3 is a wider area search, 4 is a town/city search.
   * 
   */
  public $level;

  /*
   * Latirude, if a town/city search is being applied.
   */
  public $latitude;

  /*
   * Longitude, if a town/city search is being applied.
   */
  public $longitude;

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

    // Add check here on level, perform distance search if a town/city.
    // Proceed and get all the properties in this location
    // TO DO - ensure this works in French as well
    $query->clear();

    $ordering = $this->getState('list.direction', '');

    $query = $db->getQuery(true);
    $query->select('
              h.id,
              h.parent_id,
              h.level,
              h.title as property_title,
              h.area,
              h.region,
              h.department,
              h.city,
              LEFT(h.description, 250) as description,
              h.thumbnail,
              h.occupancy,
              h.swimming,
              g.path,
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

    if ($this->level == 4 && ($ordering == 'ASC' || $ordering == 'DESC')) {
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

    $offset = $this->getState('list.start', 0); // The first result to show, i.e. the page number
    $count = $this->getState('list.limit', 10); // The number of results to show

    if ($this->level == 4 && ($ordering == 'ASC' || $ordering == 'DESC')) {
      $query->order('distance');
      $query->having('distance < 25');
    }

    // Make sure we only get live properties...
    $query->where('h.expiry_date >= ' . $db->quote($date->toSql()));

    $query->where('h.id !=1');
    // Load the results from the database.
    $db->setQuery($query, $offset, $count);
    $rows = $db->loadObjectList();

    // Process results into 
    // Push the results into cache.
    $this->store($store, $rows);

    // Return the results.
    return $this->retrieve($store);
  }

  /**
   * Method to build a database query to load the list data.
   *
   * @return  JDatabaseQuery  A database query.
   *
   * @since   2.5
   */
  protected function getListQuery() {

    // Get the date
    $date = JFactory::getDate();

    // Get the store id.
    $store = $this->getStoreId('getListQuery');

    // Use the cached data if possible.
    if ($this->retrieve($store, true)) {
      return clone($this->retrieve($store, false));
    }

    try {

      $ordering = $this->getState('list.direction', '');

      // Create a new query object.
      $db = $this->getDbo();
      $query = $db->getQuery(true);

      // Proceed and get all the properties in this location
      // TO DO - ensure this works in French as well
      $query = $db->getQuery(true);
      $query->select('
              h.id,
              h.parent_id,
              h.level,
              h.title,
              h.area,
              h.region,
              h.department,
              LEFT(h.description, 75),
              h.thumbnail,
              h.occupancy,
              h.swimming,
              c.path,
              c.title
      ');

      if ($this->level == 4 && ($ordering == 'ASC' || $ordering == 'DESC')) {
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

      if ($this->level == 4 && ($ordering == 'ASC' || $ordering == 'DESC')) {
        $query->order('distance');
        $query->having('distance < 25');
      }
      
      // Make sure we only get live properties...
      $query->where('h.expiry_date >= ' . $db->quote($date->toSql()) );

      // Push the data into cache.
      $this->store($store, $query, true);

      // Return a copy of the query object.
      return clone($this->retrieve($store, true));
    } catch (Exception $e) {

      // Oops, exceptional
    }
  }

  /**
   * Method to get the total number of results.
   *
   * @return  integer  The total number of results.
   *
   * @since   2.5
   * @throws  Exception on database error.
   */
  public function getTotal() {

    // Get the store id.
    $store = $this->getStoreId('getTotal');

    // Use the cached data if possible.
    if ($this->retrieve($store)) {
      return $this->retrieve($store);
    }

    // Get the results total.

    $total = $this->getResultsTotal();

    // Push the total into cache.
    $this->store($store, $total);

    // Return the total.
    return $this->retrieve($store);
  }

  /**
   * Method to get the total number of results for the search query.
   *
   * @return  integer  The results total.
   *
   * @since   2.5
   * @throws  Exception on database error.
   */
  protected function getResultsTotal() {
    // Get the store id.
    $store = $this->getStoreId('getResultsTotal', false);

    // Get the maximum number of results.
    $limit = (int) $this->getState('match.limit');
    // Use the cached data if possible.
    if ($this->retrieve($store)) {
      return $this->retrieve($store);
    }

    $base = $this->getListQuery();

    $sql = clone($base);


    if ($this->level == 4) {

      $this->_db->setQuery($sql);

      $results = $this->_db->execute();

      $total = $this->_db->getNumRows($results);
    } else {


      $sql->clear('select');
      $sql->select('COUNT(DISTINCT h.id)');

      // Get the total from the database.
      $this->_db->setQuery($sql);
      $total = $this->_db->loadResult();
    }







    // Push the total into cache.
    $this->store($store, min($total, $limit));

    // Return the total.
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
    $options = array();

    // Get each of the possible URL params
    // Get the query string.
    $q = !is_null($request->get('q')) ? $request->get('q', '', 'string') : $params->get('q');
    $q = $filter->clean($q, 'string');

    // Set the search term to the state, this will remember the search term (destination) the user is searching on
    $this->setState('list.searchterm', $q, 'string');

    // Load the list state.
    // Will come from the search results page.
    $this->setState('list.start', $input->get('limitstart', 0, 'uint'));
    $this->setState('list.limit', $input->get('limit', $app->getCfg('list_limit', 10), 'uint'));

    // Load the list state.
    // Will come from the search results page.
    $this->setState('list.start_date', $input->get('start_date', '', 'Alnum'));
    $this->setState('list.end_date', $input->get('end_date', '', 'Alnum'));

    // Bedrooms search options
    $this->setState('list.bedrooms', $input->get('bedrooms', '', 'int'));
    $app->setUserState('list.bedrooms', $input->get('bedrooms', '', 'int'));

    // Occupancy
    $this->setState('list.occupancy', $input->get('occupancy', '', 'int'));
    $app->setUserState('list.occupancy', $input->get('occupancy', '', 'int'));

    // Start date
    $this->setState('list.start_date', $input->get('start_date', '', 'date'));
    $app->setUserState('list.start_date', $input->get('start_date', '', 'date'));

    // End date
    $this->setState('list.end_date', $input->get('end_date', '', 'date'));
    $app->setUserState('list.end_date', $input->get('end_date', '', 'date'));

    // Load the sort direction.
    $dirn = $params->get('sort_direction', 'asc');
    switch ($dirn) {
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
  protected function getStoreId($id = '', $page = true) {
    // Default will generate store IDs based on start (i.e. page number), limit (i.e. results per page), ordering
    // Possible additional things to cache against would be
    // language
    // dates
    // prices
    // facilities
    // and so on and son on
    if ($page) {
      // Add the list state for page specific data.
      $id .= ':' . $this->getState('list.start');
      $id .= ':' . $this->getState('list.limit');
      $id .= ':' . $this->getState('list.direction');
      $id .= ':' . $this->getState('list.searchterm');
      $id .= ':' . $this->getState('list.start_date');
      $id .= ':' . $this->getState('list.end_date');
      $id .= ':' . $this->getState('list.bedrooms');
      $id .= ':' . $this->getState('list.occupancy');
    }
    return parent::getStoreId($id);
  }

}