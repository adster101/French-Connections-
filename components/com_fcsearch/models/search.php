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
  public $level = '';

  /*
   * Latitude, if a town/city search is being applied.
   */
  public $latitude = '';

  /*
   * Longitude, if a town/city search is being applied.
   */
  public $longitude = '';

  /*
   * Title the title of the locality being searched on.
   */
  public $title = '';

  /*
   * Description, the description of the locality being searched on.
   */
  public $description = '';

  public function getLocalInfo() {
    // First off we need to get the classification detail
    // E.g. is this a department, area or town etc
    // Create the query to get the search results.
    // Make this a call to get the crumbs trail?
    // Reuse the classification table instance
    // TODO - Cache the result 

    $input = JFactory::getApplication()->input;

    $lang = $input->get('lang', 'en');

    $db = $this->getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName('id') . ', ' . $db->quoteName('level') . ',latitude, longitude,' . $db->QuoteName('description') . ',' . $db->QuoteName('title'));
    if ($lang == 'fr') {
      $query->from($db->quoteName('#__classifications_translations') . ' AS t');
    } else {
      $query->from($db->quoteName('#__classifications') . ' AS t');
    }
    $query->where($db->quoteName('alias') . ' = ' . $db->quote($this->getState('list.searchterm', '')));

    // Load the result (should only be one) from the database.
    $db->setQuery($query);

    try {
      $row = $db->loadObject();
    } catch (Exception $e) {
      // Log any exception
    }

    // No results found, return an empty array
    if (empty($row)) {
      return array();
    } else {
      $this->location = $row->id;
      $this->level = $row->level;
      $this->latitude = $row->latitude;
      $this->longitude = $row->longitude;
      //$this->description = $row[4];
      //$this->title = $row[5];
      return $row;
    }
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

    $db = $this->getDbo();

    $query = $db->getQuery(true);
    // Get the date
    $date = JFactory::getDate();

    // Get the store id.
    $store = $this->getStoreId('getResults');

    // Use the cached data if possible.
    if ($this->retrieve($store)) {
      return $this->retrieve($store);
    }

    // Get the language from the state
    $lang = $this->getState('list.language', 'en');
    

    // Add check here on level, perform distance search if a town/city.
    // Proceed and get all the properties in this location
    // TO DO - ensure this works in French as well

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

      $query->from('#__helloworld h');
      if ($lang == 'fr') {
        $query->join('left', '#__classifications_translations c on c.id = h.city');
      } else {
        $query->join('left', '#__classifications c on c.id = h.city');
      }
    } else { // This else happens if the search is not on a town or city level region
      if ($lang == 'fr') {
        $query->from('#__classifications_translations c');
      } else {
        $query->from('#__classifications c');
      }
    }

    if ($this->level == 1) { // Area level
      $query->join('left', '#__helloworld h on c.id = h.area');
    } else if ($this->level == 2) { // Region level
      $query->join('left', '#__helloworld h on c.id = h.region');
    } else if ($this->level == 3) { // Department level 
      $query->join('left', '#__helloworld h on c.id = h.department');
    }
    if ($lang == 'fr') {
      $query->join('left', '#__attributes_translation b ON b.id = h.property_type');
      $query->join('left', '#__attributes_translation d ON d.id = h.accommodation_type');
      $query->join('left', '#__attributes_translation e ON e.id = h.tariff_based_on');
      $query->join('left', '#__attributes_translation f ON f.id = h.base_currency');
      $query->join('left', '#__classifications_translations g ON g.id = h.city');
    } else {
      $query->join('left', '#__attributes b ON b.id = h.property_type');
      $query->join('left', '#__attributes d ON d.id = h.accommodation_type');
      $query->join('left', '#__attributes e ON e.id = h.tariff_based_on');
      $query->join('left', '#__attributes f ON f.id = h.base_currency');
      $query->join('left', '#__classifications g ON g.id = h.city');
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
              h.id
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
      $query->where('h.expiry_date >= ' . $db->quote($date->toSql()));

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
    $storeTotal = $this->getStoreId('getResultsTotal', false);
    $storeResults = $this->getStoreId('getResultsTotalRefine', false);

    // Get the maximum number of results.
    $limit = (int) $this->getState('match.limit');
    
    // Use the cached data if possible.
    if ($this->retrieve($storeTotal)) {
      return $this->retrieve($storeTotal);
    }

    $base = $this->getListQuery();

    $sql = clone($base);

    $this->_db->setQuery($sql);

    // Execute the query so we can get a valid db resource 
    $handle = $this->_db->execute();
    
    // Get the total number returnerd
    $total = $this->_db->getNumRows($handle);
    
    // Get the actual data set?
    $resultset = $this->_db->loadObjectList();
      
    
    // Push the total into cache.
    $this->store($storeTotal, min($total, $limit));
    
    // Push the result set into the cache
    $this->store($storeResults, $resultset);
    
    // Return the total.
    return $this->retrieve($storeTotal);
  }

  /**
   * Method to retrieve a list of 'refinement options' for display on the search screen
   *  
   */
  public function getRefineOptions() {

    // The query resultset should be stored in the cache already
    $storeResults = $this->getStoreId('getResultsTotalRefine', false);
    
    // The array of property IDs we have results for, for this particular query
    $property_list = array();
    
    
    // Use the cached data if possible.
    if ($this->retrieve($storeResults)) {
      
      $resultset = $this->retrieve($storeResults);
      
      foreach ($resultset as $result) {
        $property_list[] = $result->id;
      }
      
    }    
    
    $property_list = implode(',',$property_list);
    
    
    
    try {

      $attributes = array();
      $lang = $this->getState('list.language', 'en');
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      // Retrieve based on the language
      if ($lang == 'fr') {
        $query->select('a.id,count(attribute_id) as count, c.title as attribute, a.published, at.title as facility_type, at.search_code');
      } else {
        $query->select('a.id,count(attribute_id) as count, a.title AS attribute, a.published, at.title as facility_type, at.search_code');
      }

      $query->from('#__attributes AS a');
      $query->join('left', '#__attributes_type at on at.id = a.attribute_type_id');
      $query->join('left', '#__attributes_property ap on ap.attribute_id = a.id');

      // If any other language that en-GB load in the translation based on the lang->getTag() function...
      if ($lang == 'fr') {
        $query->join('LEFT', $db->quoteName('#__attributes_translation') . ' c on c.id = a.id');
      }

      $query->where('search_filter = 1');
      $query->where('a.published = 1');
      $query->where('property_id in (' . $property_list . ')');
      $query->group('a.id');
      
      // Get the options.
      $db->setQuery($query);




      $facilities = $db->loadObjectList();

      foreach ($facilities as $facility) {
        if (!array_key_exists($facility->facility_type, $attributes)) {
          $attributes[$facility->facility_type] = array();
        }

        $attributes[$facility->facility_type][$facility->attribute]['count'] = $facility->count;
        $attributes[$facility->facility_type][$facility->attribute]['search_code'] = $facility->search_code;
        $attributes[$facility->facility_type][$facility->attribute]['id'] = $facility->id;

      }     
      return $attributes;
      
    } catch (Exception $e) {
      print_r($e->getMessage());
      // Log the exception and return false
      //JLog::add('Problem fetching facilities for - ' . $id . $e->getMessage(), JLOG::ERROR, 'facilities');
      return false;
    }
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
   * 
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

    // Set the language in the model state    
    $this->setState('list.language', $input->get('lang', 'en'));

    // Get each of the possible URL params
    // Get the query string.
    $q = !is_null($request->get('s_kwds')) ? $request->get('s_kwds', '', 'string') : $params->get('s_kwds');
    $q = $app->stringURLSafe($filter->clean($q, 'string'));

    // Set the search term to the state, this will remember the search term (destination) the user is searching on
    $this->setState('list.searchterm', $q, 'string');

    // Load the list state.
    // Will come from the search results page.
    $this->setState('list.start', $input->get('limitstart', 0, 'uint'));
    $this->setState('list.limit', $input->get('limit', $app->getCfg('list_limit', 10), 'uint'));

    // Load the list state.
    // Will come from the search results page.
    $this->setState('list.arrival', str_replace('arrival_', '', $input->get('arrival', '', 'date')));
    $app->setUserState('list.arrival', str_replace('arrival_', '', $input->get('arrival', '', 'date')));

    $this->setState('list.departure', str_replace('departure_', '', $input->get('departure', '', 'date')));
    $app->setUserState('list.departure', str_replace('departure_', '', $input->get('departure', '', 'date')));

    // Bedrooms search options
    $this->setState('list.bedrooms', $input->get('bedrooms', '', 'int'));
    $app->setUserState('list.bedrooms', $input->get('bedrooms', '', 'int'));
    
    $this->setState('list.activity', $input->get('activity', '', 'int'));

    // Occupancy
    $this->setState('list.occupancy', $input->get('occupancy', '', 'int'));
    $app->setUserState('list.occupancy', $input->get('occupancy', '', 'int'));

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
      $id .= ':' . $this->getState('list.language');
    }
    return parent::getStoreId($id);
  }

}