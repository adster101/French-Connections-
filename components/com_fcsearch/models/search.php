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
  public $location;

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
  public $currencies = '';

  public function __construct($config = array()) {

    parent::__construct($config);

    $this->currencies = $this->getCurrencyConversions();

    // Set the default search and what not here?
  }

  /**
   * Get the information about the area which is being searched on.
   *  
   * @return boolean
   */
  public function getLocalInfo() {

    // If the search term is an int then we just redirect to the accommodation view
    $app = JFactory::getApplication();

    if ((int) $this->getState('list.searchterm', '')) {
      $app->redirect('/listing/' . $this->getState('list.searchterm', ''));
    }

    if (!$this->getState('list.searchterm', '')) {
      return false;
    }


    // No cached data available so load it up.
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

    // See if we got a valid search 
    $row = $db->loadObject();

    // If no results then throw an exception 
    if (empty($row)) {

      return false;
    }

    // Must have a result set
    // Stash it in the model state
    $this->setState('search.location', $row->id);
    $this->setState('search.level', $row->level);
    $this->setState('search.latitude', $row->latitude);
    $this->setState('search.longitude', $row->longitude);

    return $row;
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

    // Get the date
    $date = JFactory::getDate();

    // Get the store id.
    $store = $this->getStoreId('getResults');

    // Use the cached data if possible.
    if ($this->retrieve($store)) {
      return $this->retrieve($store);
    }


    $base = $this->getListQuery();

    $sql = clone($base);

    $offset = $this->getState('list.start', 0); // The first result to show, i.e. the page number
    $count = $this->getState('list.limit', 10); // The number of results to show
    // Load the results from the database.
    $db->setQuery($sql, $offset, $count);


    $rows = $db->loadObjectList();

    // Process results into
    // Push the results into cache.
    $this->store($store, $rows);

    // Return the results.
    return $this->retrieve($store);
  }

  /*
   * Method to build out a query which, when executed, will return a list of propert
   *
   * @return  JDatabaseQuery  A database query.
   *
   * @since   2.5
   *
   */

  protected function getListQuery() {

    $date = date('Y-m-d');

    // Get the store id.
    $store = $this->getStoreId('getListQuery');

    // Get the language from the state
    $lang = $this->getState('list.language', 'en');

    // Use the cached data if possible.
    if ($this->retrieve($store, true)) {
      return clone($this->retrieve($store, false));
    }

    try {


      $sort_column = $this->getState('list.sort_column', '');
      $sort_order = $this->getState('list.direction', '');

      // Create a new query object.
      $db = $this->getDbo();
      $query = $db->getQuery(true);

      $query->select('
        a.id,
        c.unit_id,
        c.unit_title,
        b.published_on,
        b.title,
        b.area,
        b.region,
        b.department,
        b.latitude,
        b.longitude,
        b.city,
        c.occupancy,
        i.path,
        left(c.description,500) as description,
        i.title as location_title,
        (single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms) as bedrooms,
        case 
          when c.base_currency = \'EUR\'
             THEN 
               (select min(tariff) * ' . $this->currencies["GBP"]->exchange_rate . ' from qitz3_tariffs t where t.unit_id = j.id and end_date > ' . $db->quote($date) . ' limit 0,1)
             ELSE
              (select min(tariff) from qitz3_tariffs t where t.unit_id = j.id and end_date > ' . $db->quote($date) . ' limit 0,1)

             END as price,
        
        (select count(unit_id) from qitz3_reviews where unit_id = c.unit_id ) as reviews,
        l.title as accommodation_type,
        k.title as property_type,
        g.title as tariff_based_on,
        o.image_file_name as thumbnail
      ');

      $query->from('#__property a');
      $query->join('left', '#__unit j on a.id = j.property_id');

      $query->join('left', '#__property_versions b on ( b.property_id = a.id and b.review = 0 )'); // This should be okay here as should only ever have one version with review = 
      $query->join('left', '#__unit_versions c on ( c.unit_id = j.id and c.review = 0)');

      // Join the images, innit!
      $query->join('left', '#__property_images_library o on c.id = o.version_id');
      $query->where('(o.ordering = 1 or o.ordering is null)');
            
      // Need to switch these based on the language
      //if ($lang == 'fr') {
      //$query->from('#__classifications_translations c');
      //} //else {
      //$query->from('#__classifications c');
      //}
      // Need to switch the below based on the level e.g. department or whatever

      if ($this->getState('search.level') == 1) { // Country level
        $query->join('left', '#__classifications as d on d.id = b.country');
        $query->where('b.country = ' . $this->getState('search.location', ''));
      } elseif ($this->getState('search.level') == 2) { // Area level
        $query->join('left', '#__classifications as d on d.id = b.area');
        $query->where('b.area = ' . $this->getState('search.location', ''));
      } elseif ($this->getState('search.level') == 3) { // Region level
        $query->join('left', '#__classifications as d on d.id = b.region');
        $query->where('b.region= ' . $this->getState('search.location', ''));
      } elseif ($this->getState('search.level') == 4) { // Department level
        $query->join('left', '#__classifications as d on d.id = b.department');
        $query->where('b.department = ' . $this->getState('search.location', ''));
      }

      $query->join('left', '#__attributes k on k.id = c.property_type');

      $query->join('left', '#__attributes l on l.id = c.accommodation_type');

      $query->join('left', '#__attributes g on g.id = c.tariff_based_on');
      $query->join('left', '#__classifications i ON i.id = b.city');

      if ($this->getState('search.level') == 5) {
        // Add the distance based bit in as this is a town/city search
        $query->select('
        ( 3959 * acos(cos(radians(' . $this->getState('search.longitude', '') . ')) *
          cos(radians(b.latitude)) *
          cos(radians(b.longitude) - radians(' . $this->getState('search.latitude', '') . '))
          + sin(radians(' . $this->getState('search.longitude', '') . '))
          * sin(radians(b.latitude)))) AS distance
        ');
      }

      if ($this->getState('list.arrival')) {
        $query->join('left', '#__availability arr on c.unit_id = arr.unit_id');
        $query->where('arr.start_date <= ' . $db->quote($this->getState('list.arrival', '')));
        $query->where('arr.end_date >= ' . $db->quote($this->getState('list.departure', '')));
        $query->where('arr.availability = 1');
      }

      if ($this->getState('list.bedrooms')) {
        $query->where('( single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms ) = ' . $this->getState('list.bedrooms', ''));
      }

      if ($this->getState('list.occupancy')) {
        $query->where('c.occupancy >= ' . $this->getState('list.occupancy', ''));
      }

      if ($this->getState('list.property_type')) {
        $query->where('c.property_type in (10)');
      }

      // Sort out the ordering required
      if ($sort_column) {
        $query->order($sort_column . ' ' . $sort_order);
      } elseif ($this->getState('search.level') == 5) {
        $query->order('distance');
        $query->having('distance < 30');
      }

      // Sort out the budget requirements
      $min_price = $this->getState('list.min_price', '');
      if (!empty($min_price)) {
        $query->having('price > ' . $min_price);
      }

      // Sort out the budget requirements
      $max_price = $this->getState('list.max_price', '');
      if (!empty($max_price)) {
        $query->having('price < ' . $max_price);
      }
      

      // Apply the rest of the filter, if there are any
      $query = $this->getFilterState('activities', $query, '#__property_attributes');
      $query = $this->getFilterState('suitability', $query);
      $query = $this->getFilterState('external_facilities', $query);
      $query = $this->getFilterState('property_facilities', $query);
      $query = $this->getFilterState('kitchen', $query);
      
      //$query = $this->getFilterState('property_type', $query);
      //$query = $this->getFilterState('accommodation_type', $query);
      // Make sure we only get live properties...
      $query->where('a.expiry_date >= ' . $db->quote($date));
      $query->where('j.published = 1');
      $query->where('c.unit_id is not null');

      // Push the query into the cache.
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
  public function getResultsTotal() {

    // Get the store id.
    $store_list = $this->getStoreId('getPropertyList');
    $store = $this->getStoreId('getPropertyListTotal');

    // Get the maximum number of results.
    $limit = (int) $this->getState('match.limit');

    // Use the cached data if possible.
    if ($this->retrieve($store)) {
      return $this->retrieve($store);
    }

    // Total isn't available in the cache
    // Get the list of properties, using the cached data if possible.

    if ($this->retrieve($store_list)) {

      $total = count(explode(',', $this->retrieve($store_list)));
    }

    // Push the total into cache.
    $this->store($store, min($total, $limit));

    // Return the total.
    return $this->retrieve($store);
  }

  /*
   * Method to return a list of propery IDs based on the search filter applied by the user
   * This list is then used to populate the new search filter with counts etc
   *
   * @return  array   The list of property results.
   *
   * @throws  Exception on database error.
   *
   */

  public function getPropertyList($markers = false) {

    // Get the store ID
    $store = $this->getStoreId('getPropertyList');

    // Use the cached data if possible.
    if ($this->retrieve($store)) {
      return $this->retrieve($store);
    }

    // Property list array
    $property_list = array();

    // Get the query to run
    $query = $this->getListQuery();

    $query->clear('select');
    $query->clear('order');

    $query->select('c.id');

    if ($this->getState('search.level') == 5) {
      // Add the distance based bit in as this is a town/city search
      $query->select('
        ( 3959 * acos(cos(radians(' . $this->getState('search.longitude', '') . ')) *
          cos(radians(b.latitude)) *
          cos(radians(b.longitude) - radians(' . $this->getState('search.latitude', '') . '))
          + sin(radians(' . $this->getState('search.longitude', '') . '))
          * sin(radians(b.latitude)))) AS distance
        ');
      $query->order('distance asc');
      $query->having('distance < 30');
    }

    // No cached results so proceed
    $db = $this->getDbo();

    // Load the results from the database.
    $db->setQuery($query);

    // Get the flipping results
    $rows = $db->loadObjectList();

    foreach ($rows as $result) {
      $property_list[] = $result->id;
    }

    $property_list_str = implode(',', $property_list);

    // Push the results into cache.
    $this->store($store, $property_list_str);

    // Return the results.
    return $this->retrieve($store);
  }

  /**
   * Method to pull out the property type based drilldowns
   */
  public function getRefinePropertyOptions() {
    
    // Okay, so we can't use the 'property list' technique here because we're actually interested
    // in the properties not in the property list. If that makes sense?
    // E.g. 200 props in a search, 50 of which are villas, we are interested in the 150 that aren't
    // villas so we can refine further on those if we want to?
    
  }
  
  
  /**
   * Method to pull out the location based drilldowns for refine search
   * 
   */
  public function getRefineLocationOptions() {

    // The query resultset should be stored in the local model cache already
    $store_list = $this->getStoreId('getPropertyList');

    // Create a store ID to get the actual options, if they are already cached, which they might be
    $store = $this->getStoreId('getRefineOptions');

    // Get the cached data for this method
    if ($this->retrieve($store)) {
      return $this->retrieve($store);
    }

    // Cached data not available so proceed
    // Retrieve the list of properties for this search from the cache
    if ($this->retrieve($store_list)) {
      $property_list = $this->retrieve($store_list);
    }

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('c.title, count(*) as count');
    $query->from('#__property_versions a');
    $query->join('left', '#__unit_versions b on a.property_id = b.property_id');
    if ($this->getState('search.level') == 1) { // Country level
      $query->join('left', '#__classifications c on c.id = a.area');
      $query->group('a.area');
    } elseif ($this->getState('search.level') == 2) { // Area level
      $query->join('left', '#__classifications c on c.id = a.region');
      $query->group('a.region');
    } elseif ($this->getState('search.level') == 3) { // Region level
      $query->join('left', '#__classifications c on c.id = a.department');
      $query->group('a.department');
    } elseif ($this->getState('search.level') == 4) { // Department level
      $query->join('left', '#__classifications c on c.id = a.city');
      $query->group('a.city');
    } elseif ($this->getState('search.level') == 5) { // City level
      $query->join('left', '#__classifications c on c.id = a.city');
      $query->group('a.city');
    }
    $query->where('b.id in (' . $property_list . ')');


    // Get the options.
    $db->setQuery($query);

    $locations = $db->loadObjectList();

    if (!$locations) {
      return false;
    }

    return $locations;
  }

  /**
   * Method to retrieve a list of 'refinement options' for display on the search page
   *
   */
  public function getRefineAttributeOptions() {

    // The query resultset should be stored in the local model cache already
    $store_list = $this->getStoreId('getPropertyList');

    // Create a store ID to get the actual options, if they are already cached, which they might be
    $store = $this->getStoreId('getAttributeRefineOptions');

    // Get the cached data for this method
    if ($this->retrieve($store)) {
      return $this->retrieve($store);
    }

    // Cached data not available so proceed
    // Retrieve the list of properties for this search from the cache
    if ($this->retrieve($store_list)) {
      $property_list = $this->retrieve($store_list);
    }

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
      $query->join('left', '#__unit_attributes ap on ap.attribute_id = a.id');

      // If any other language that en-GB load in the translation based on the lang->getTag() function...
      if ($lang == 'fr') {
        $query->join('LEFT', $db->quoteName('#__attributes_translation') . ' c on c.id = a.id');
      }

      $query->where('search_filter = 1');
      $query->where('a.published = 1');
      $query->where('version_id in (' . $property_list . ')');
      $query->group('a.id');
      //$query->order('count desc');
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

      /*
       * Wrap this into a db query so we can use the ordering set on the component, or parameterise it.
       */
      $order = array(
          1 => 'Property Type',
          2 => 'Accommodation Type',
          3 => 'Suitability',
          4 => 'External Facilities',
          5 => 'Property Facilities',
          //6 => 'Activities nearby',
              //7 => 'Kitchen features',
              //8 => 'Location Type'
      );

      $output = array();

      foreach ($attributes as $array) {
        foreach ($order as $field) {
          $output[$field] = $attributes[$field];
        }
      }

      $sorted_attributes = $output;

      // Push the results into cache.
      $this->store($store, $sorted_attributes);

      // Return the total.
      return $this->retrieve($store);
    } catch (Exception $e) {
      // Log the exception and return false
      //JLog::add('Problem fetching facilities for - ' . $id . $e->getMessage(), JLOG::ERROR, 'facilities');
      return false;
    }
  }

  /*
   * Method to get a load of marker information based on getPropertyList
   *
   * @return array  A list of property ids and associated info
   *
   */

  public function getMapMarkers() {

    // The query resultset should be stored in the local model cache already
    $store = $this->getStoreId('getMapMarkers');

    // Get the info from the cache if we can
    if ($this->retrieve($store)) {
      return $this->retrieve($store);
    }

    // No data in the cache so let's get the list of markers.
    $markers = $this->getPropertyList($markers = true);


    // Push the results into cache.
    $this->store($store, $markers);

    // Return the total.
    return $this->retrieve($store);
  }

  /*
   * Method to build a query for the getMapMarkers query to use, to, like, get the map markers
   *
   * @return  object  Returns a query to get a list of map markers...should probably be refined to get 10 at a time...
   *
   */

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
  protected function store($id, $data) {

    // Store the data in internal cache.
    $this->cache[$id] = $data;

    $params = $this->state->get('parameters.menu');
    $lifetime = $params->get('cache_time', '');
    $persistent = $params->get('cache', '');

    // Store the data in external cache if data is persistent.
    if ($persistent) {
      $cache = JFactory::getCache($this->context, 'output');
      $cache->setCaching(true);
      $cache->setLifeTime($lifetime);
      return $cache->store(serialize($data), $id);
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
  public function retrieve($id, $persistent = true) {
    $data = null;

    // Use the internal cache if possible.
    if (isset($this->cache[$id])) {
      return $this->cache[$id];
    }

    $params = $this->state->get('parameters.menu');
    $persistent = $params->get('cache', '');

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
  public function populateState($ordering = null, $direction = null) {
    // Get the configuration options.
    $app = JFactory::getApplication();
    $input = $app->input;

    $params = $app->getParams();
    $user = JFactory::getUser();
    // Should apply this filter to other params here as well...
    $filter = JFilterInput::getInstance();
    $this->setState('filter.language', $app->getLanguageFilter());

    // Set the language in the model state
    $this->setState('list.language', $input->get('lang', 'en'));

    // Get each of the possible URL params
    // Get the query string.
    $tmp = !is_null($input->get('s_kwds')) ? $input->get('s_kwds', '', 'string') : $params->get('s_kwds', 'france');
    $q = JApplication::stringURLSafe($filter->clean($tmp, 'string'));

    // Set the search term to the state, this will remember the search term (destination) the user is searching on
    $this->setState('list.searchterm', $q, 'string');
    $app->setUserState('list.searchterm', $q, 'string');

    // Set the list starting page, for the pagination
    $this->setState('list.start', $input->get('limitstart', 0, 'uint'));
    $app->setUserState('list.start', $input->get('limitstart', 0, 'uint'));

    // The list limit (number of results) isn't settable by the user so just take it from the config
    $this->setState('list.limit', $input->get('limit', $app->getCfg('list_limit', 10), 'uint'));

    // Will come from the search results page.
    $arrival = str_replace('arrival_', '', $input->get('arrival', '', 'date'));
    $arrival_date = ($arrival) ? JFactory::getDate($arrival)->calendar('Y-m-d') : '';

    $departure = str_replace('departure_', '', $input->get('departure', '', 'date'));
    $departure_date = ($departure) ? JFactory::getDate($departure)->calendar('Y-m-d') : '';

    $this->setState('list.arrival', $arrival_date);
    $app->setUserState('list.arrival', $arrival_date);

    $this->setState('list.departure', $departure_date);
    $app->setUserState('list.departure', $departure_date);

    // Bedrooms search options
    $this->setState('list.bedrooms', $input->get('bedrooms', '', 'int'));
    $app->setUserState('list.bedrooms', $input->get('bedrooms', '', 'int'));

    // Occupancy
    $this->setState('list.occupancy', $input->get('occupancy', '', 'int'));
    $app->setUserState('list.occupancy', $input->get('occupancy', '', 'int'));

    // Property type
    $this->setState('list.property_type', $input->get('property', '', 'array'));
    $app->setUserState('list.property_type', $input->get('property', '', 'array'));

    // Accommodation type
    $this->setState('list.accommodation_type', $input->get('accommodation', '', 'array'));
    $app->setUserState('list.accommodation_type', $input->get('accommodation', '', 'array'));

    // Budget and price, innit!
    $this->setState('list.min_price', $input->get('min', '', 'int'));
    $app->setUserState('list.min_price', $input->get('min', '', 'array'));

    // Budget and price, innit!
    $this->setState('list.max_price', $input->get('max', '', 'int'));
    $app->setUserState('list.max_price', $input->get('max', '', 'array'));

    // Load the sort direction.
    $dirn = $input->get('order', array(), 'array');

    if (!empty($dirn) && $dirn[0] !== '') {
      $sort_order = explode('_', $dirn[0]);
      $this->setState('list.sort_column', $sort_order[1]);
      $this->setState('list.direction', $sort_order[2]);
    }

    // Set the match limit.
    $this->setState('match.limit', 10000);

    // Get the rest of the filter options such as property type, facilities and activites etc.
    $activities = $input->get('activities', '', 'array');
    $property_facilities = $input->get('internal', '', 'array');
    $external_facilities = $input->get('external', '', 'array');
    $kitchen_facilities = $input->get('kitchen', '', 'array');
    //$property_type = $input->get('property', '', 'array');
    //$accommodation_type = $input->get('accommodation', '', 'array');
    $suitability = $input->get('suitability', '', 'array');

    // populateFilterState pushes all the filter IDs into the state
    $this->populateFilterState($activities, 'activities');
    $this->populateFilterState($property_facilities, 'property_facilities');
    $this->populateFilterState($external_facilities, 'external_facilities');
    $this->populateFilterState($kitchen_facilities, 'kitchen_facilities');
    //$this->populateFilterState($property_type, 'property_type');
    //$this->populateFilterState($accommodation_type, 'accommodation_type');
    $this->populateFilterState($suitability, 'suitability');

    // Load the parameters.
    $this->setState('params', $params);

    // Load the user state.
    $this->setState('user.id', (int) $user->get('id'));
    $this->setState('user.groups', $user->getAuthorisedViewLevels());
  }

  /*
   * Method to generate the filter state ids for later filtering in the db
   *
   */

  private function populateFilterState($input, $label) {

    if (is_array($input)) {

      $ids = array();

      foreach ($input as $filter) {
        // Assume that this is in the form of e.g. activity_Golf_51
        $id = (int) array_pop(explode('_', $filter));

        $ids[] = $id;
      }

      $this->setState('list.' . $label, $ids);
    } elseif (!empty($input)) {

      $id = (int) array_pop(explode('_', $input));

      $this->setState('list.' . $label, $id);
    }
  }

  /*
   * Method to generate the various filter options,
   * add them to the query and then return the query object
   *
   * @return  query  The search query being built
   *
   */

  private function getFilterState($filter = '', JDatabaseQueryMysqli $query, $attributes_table = '#__unit_attributes') {

    if (empty($filter)) {
      return false;
    }

    if (empty($query)) {
      return false;
    }

    // Add the activities filter to the query
    if ($this->getState('list.' . $filter, array())) {

      $filters = $this->getState('list.' . $filter);

      if (is_array($filters)) {

        foreach ($filters as $key => $value) {
          $query->join('left', $attributes_table . ' ap' . $value . ' ON ap' . $value . '.property_id = c.unit_id');
          $query->where('ap' . $value . '.attribute_id = ' . (int) $value);
        }
      } else {
        $query->join('left', $attributes_table . ' ' . $filter . ' ON apact.property_id = c.unit_id');
        $query->where($filter . '.attribute_id = ' . $this->getState('list. ' . $filter));
      }
    }

    return $query;
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
  public function getStoreId($id = '', $page = true) {
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
      $id .= ':' . $this->getState('list.sort_column');
      $id .= ':' . $this->getState('list.direction');
      $id .= ':' . $this->getState('list.searchterm');
      $id .= ':' . $this->getState('list.start_date');
      $id .= ':' . $this->getState('list.end_date');
      $id .= ':' . $this->getState('list.bedrooms');
      $id .= ':' . $this->getState('list.occupancy');
      $id .= ':' . $this->getState('list.language');
      $id .= ':' . $this->getState('list.property_type');
      $id .= ':' . $this->getState('list.accommodation_type');
      $id .= ':' . $this->getState('list.max_price');
      $id .= ':' . $this->getState('list.min_price');

      // Get each of the filter attribute id and build that into the cache key...
      $activities = $this->getState('list.activities', '');
      $property_facilities = $this->getState('list.property_facilities', '');
      $external_facilities = $this->getState('list.external_facilities', '');
      $kitchen_facilities = $this->getState('list.kitchen_facilities', '');

      // For the activities...
      if (is_array($activities)) {
        foreach ($activities as $key => $activity) {
          $id .= ':' . $activity;
        }
      } elseif ($activities) {
        $id .= ':' . $activities;
      }

      // For the property facilities...
      if (is_array($property_facilities)) {
        foreach ($property_facilities as $key => $facility) {
          $id .= ':' . $facility;
        }
      } else {
        $id .= ':' . $property_facilities;
      }

      // For the external facilities...
      if (is_array($external_facilities)) {
        foreach ($external_facilities as $key => $external_facility) {
          $id .= ':' . $external_facility;
        }
      } else {
        $id .= ':' . $external_facilities;
      }

      // For the property facilities...
      if (is_array($kitchen_facilities)) {
        foreach ($kitchen_facilities as $key => $kitchen_facility) {
          $id .= ':' . $kitchen_facility;
        }
      } else {
        $id .= ':' . $kitchen_facilities;
      }
    }

    return $id;
  }

  /*
   * Get a list of the currency conversions
   *
   * @return object An object containing the conversion rates from EUR to GBP and USD
   *
   */

  protected function getCurrencyConversions() {

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    try {
      $query->select('currency, exchange_rate');
      $query->from('#__currency_conversion');

      $db->setQuery($query);

      $results = $db->loadObjectList($key = 'currency');
    } catch (Exception $e) {
      // Log this error
    }


    return $results;
  }

}