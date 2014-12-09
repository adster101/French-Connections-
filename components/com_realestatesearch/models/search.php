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
class RealestateSearchModelSearch extends JModelList
{

  /**
   * Context string for the model type
   *
   * @var    string
   * @since  2.5
   */
  protected $context = 'com_realestatesearch.search';

  /**
   * The location integer is the classification id
   *
   * @var   query
   * @since  2.5
   */
  public $location;

  /*
   * The 'level' of the search. 1-4 is a wider area search, 5 is a town/city search.
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
   * The date which we check the expiry date against.
   */
  public $data = '';

  /*
   * The Item ID of the menu item
   */
  public $itemid = '';
  
  /*
   * Description, the description of the locality being searched on.
   */
  public $description = '';
  
  public $currencies = '';

  public function __construct($config = array())
  {

    parent::__construct($config);

    $this->currencies = $this->getCurrencyConversions();

    $this->date = JFactory::getDate()->calendar('Y-m-d');

    $this->itemid = SearchHelper::getItemid(array('component', 'com_realestatesearch'));

    // Set the default search and what not here?
  }

  public function getLogSearch()
  {

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->insert('#__realestate_search_log');
    $query->columns('location_id, date_created');

    $location_id = $this->getState('search.location', '');
    $date = JFactory::getDate()->calendar('Y-m-d');

    $query->values((int) $location_id . ',' . $db->quote($date));

    $db->setQuery($query);

    try
    {
      $db->execute();
    }
    catch (RuntimeException $e)
    {
      // TO DO log me baby
      return false;
    }

    return true;
  }

  /**
   * Get the information about the area which is being searched on.
   * TO DO - This should probably be a protected method called internally from say getResults
   * if state not set accordingly.
   * 
   * @return boolean
   */
  public function getLocalInfo()
  {

    // If the search term is an int then we just redirect to the accommodation view
    $app = JFactory::getApplication();

    if ((int) $this->getState('list.searchterm', ''))
    {
      $ItemId = SearchHelper::getItemid(array('component','com_realestate'));
      $app->redirect(JRoute::_('index.php?option=com_realestate&Itemid=' . $ItemId . '&id=' . $this->getState('list.searchterm', '')));
    }

    if (!$this->getState('list.searchterm', ''))
    {
      return false;
    }

    //$store = $this->getStoreId('getLocalInfo');
    // Use the cached data if possible.
    //if ($this->retrieve($store)) {
    //return $this->retrieve($store);
    //}
    // No cached data available so load it up.
    $input = JFactory::getApplication()->input;

    $lang = $input->get('lang', 'en');

    $db = $this->getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName('id') . ', ' . $db->quoteName('level') . ',latitude, longitude,' . $db->QuoteName('description') . ',' . $db->QuoteName('title'));
    if ($lang == 'fr')
    {
      $query->from($db->quoteName('#__classifications_translations') . ' AS t');
    }
    else
    {
      $query->from($db->quoteName('#__classifications') . ' AS t');
    }
    $query->where($db->quoteName('alias') . ' = ' . $db->quote($this->getState('list.searchterm', '')));

    // Load the result (should only be one) from the database.
    $db->setQuery($query);

    // See if we got a valid search 
    $row = $db->loadObject();

    if (!$row)
    {
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
  public function getResults()
  {

    $db = $this->getDbo();

    // Get the date
    $date = JFactory::getDate();

    // Get the store id.
    $store = $this->getStoreId('getResults');

    // Use the cached data if possible.
    if ($this->retrieve($store))
    {
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

  protected function getListQuery()
  {

    // Get the store id.
    $store = $this->getStoreId('getListQuery');

    // Get the language from the state
    $app = JFactory::getApplication();
    $lang = $app->getLanguage()->getTag();

    // Use the cached data if possible.
    if ($this->retrieve($store, true))
    {
      return clone($this->retrieve($store, false));
    }

    try
    {

      $sort_column = $this->getState('list.sort_column', '');
      $sort_order = $this->getState('list.direction', '');

      // Create a new query object.
      $db = $this->getDbo();
      $query = $db->getQuery(true);

      $query->select('
        a.id as property_id,
        b.*,
        (b.double_bedrooms + b.single_bedrooms) as bedrooms,
        j.title as location_title,
        e.image_file_name as thumbnail

      ');

      $query->from('#__realestate_property as a');

      $query->leftJoin('#__realestate_property_versions b ON (b.realestate_property_id = a.id and b.id = (select max(d.id) from #__realestate_property_versions as d where d.realestate_property_id = a.id and d.review = 0))');

      if ($this->getState('search.level') == 5)
      {
        $query->select('ROUND(3959 * acos(cos(radians(' . $this->getState('search.longitude', '') . ')) *
          cos(radians(b.latitude)) *
          cos(radians(b.longitude) - radians(' . $this->getState('search.latitude', '') . '))
          + sin(radians(' . $this->getState('search.longitude', '') . '))
          * sin(radians(b.latitude))),1)  as distance');
      }

     
      // Join the images, innit!
      $query->join('left', '#__realestate_property_images_library e on b.id = e.version_id');
      $query->where('(e.ordering = 1)');

      // Join the translations table to pick up any translations 
      if ($lang == 'fr-FR')
      {
        $query->select('k.unit_title, k.description');
        $query->join('left', '#__unit_versions_translations k on k.version_id = d.id');
        $query->join('left', '#__classifications_translations j ON j.id = c.city');
      }
      else
      {
        $query->join('left', '#__classifications j ON j.id = b.city');
      }

      if ($this->getState('search.level') == 1)
      { // Country level
        $query->join('left', '#__classifications as f on f.id = b.country');
        $query->where('b.country = ' . $this->getState('search.location', ''));
      }
      elseif ($this->getState('search.level') == 2)
      { // Area level
        $query->join('left', '#__classifications as f on f.id = b.area');
        $query->where('b.area = ' . $this->getState('search.location', ''));
      }
      elseif ($this->getState('search.level') == 3)
      { // Region level
        $query->join('left', '#__classifications as f on f.id = b.region');
        $query->where('b.region= ' . $this->getState('search.location', ''));
      }
      elseif ($this->getState('search.level') == 4)
      { // Department level
        $query->join('left', '#__classifications as f on f.id = b.department');
        $query->where('b.department = ' . $this->getState('search.location', ''));
      }
      elseif ($this->getState('search.level') == 5)
      {
        // Add the distance based bit in as this is a town/city search
        $query->where('
        ( 3959 * acos(cos(radians(' . $this->getState('search.longitude', '') . ')) *
          cos(radians(b.latitude)) *
          cos(radians(b.longitude) - radians(' . $this->getState('search.latitude', '') . '))
          + sin(radians(' . $this->getState('search.longitude', '') . '))
          * sin(radians(b.latitude))) < 10)
        ');

        $query->order('
        ( 3959 * acos(cos(radians(' . $this->getState('search.longitude', '') . ')) *
          cos(radians(b.latitude)) *
          cos(radians(b.longitude) - radians(' . $this->getState('search.latitude', '') . '))
          + sin(radians(' . $this->getState('search.longitude', '') . '))
          * sin(radians(b.latitude))) ) 
        ');
      }

      if ($this->getState('list.bedrooms'))
      {
        $bedrooms = $this->getState('list.bedrooms', '');
        $query = $this->getFilterBedrooms($query, $bedrooms, $db);
      }

      // Sort out the budget requirements
      if ($this->getState('list.min_price') || $this->getState('list.max_price'))
      {
        $min_price = $this->getState('list.min_price', '');
        $max_price = $this->getState('list.max_price', '');
        $query = $this->getFilterPrice($query, $min_price, $max_price, $db);
      }

      // Sort out the ordering required      
      // No filter function needed here as ordering can simplt be cleared and reinstated, if needed.
      if ($sort_column)
      {
        $query->clear('order');
        $query->order($sort_column . ' ' . $sort_order);
      }

      // Make sure we only get live properties...
      $query->where('a.expiry_date >= ' . $db->quote($this->date));
      $query->where('a.published = 1');
      $query->where('b.review = 0');
      // Push the query into the cache.
      $this->store($store, $query, true);

      // Return a copy of the query object.
      return clone($this->retrieve($store, true));
    }
    catch (Exception $e)
    {
      // Oops, exceptional
    }
  }

 
  /**
   * Method to pull out the location based drilldowns for refine search
   * 
   */
  public function getRefineLocationOptions()
  {

    // Create a store ID to get the actual options, if they are already cached, which they might be
    $store = $this->getStoreId('getRefineLocationOptions');

    // Get the cached data for this method
    if ($this->retrieve($store))
    {
      return $this->retrieve($store);
    }

    // Cached data not available so proceed
    // Retrieve the list of properties for this search from the cache
    if ($this->retrieve($store))
    {
      $locations = $this->retrieve($store);
    }

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('e.title, count(e.id) as count');
    $query->from('#__realestate_property a');
    $query->leftJoin('#__realestate_property_versions b on b.realestate_property_id = a.id');

    if ($this->getState('search.level') == 1)
    { // Country level
      $query->join('left', '#__classifications e on e.id = b.area');
      $query->group('b.area');
      $query->where('b.country = ' . (int) $this->getState('search.location'));
    }
    elseif ($this->getState('search.level') == 2)
    { // Area level
      $query->join('left', '#__classifications e on e.id = b.region');
      $query->group('b.region');
      $query->where('b.area = ' . (int) $this->getState('search.location'));
    }
    elseif ($this->getState('search.level') == 3)
    { // Region level
      $query->join('left', '#__classifications e on e.id = b.department');
      $query->group('b.department');
      $query->where('b.region = ' . (int) $this->getState('search.location'));
    }
    elseif ($this->getState('search.level') == 4)
    { // Department level
      $query->join('left', '#__classifications e on e.id = b.city');
      $query->group('b.city');
      $query->where('b.department = ' . (int) $this->getState('search.location'));
    }
    elseif ($this->getState('search.level') == 5)
    { // City level
      $query->join('left', '#__classifications e on e.id = b.city');
      $query->group('b.city');
    }

    if ($this->getState('list.bedrooms'))
    {
      $bedrooms = $this->getState('list.bedrooms', '');
      $query = $this->getFilterBedrooms($query, $bedrooms, $db);
    }
    // Sort out the budget requirements
    if ($this->getState('list.min_price') || $this->getState('list.max_price'))
    {
      $min_price = $this->getState('list.min_price', '');
      $max_price = $this->getState('list.max_price', '');
      $query = $this->getFilterPrice($query, $min_price, $max_price, $db);
    }

    // Make sure we only get live properties...
    $query->where('a.expiry_date >= ' . $db->quote($this->date));
    $query->where('a.published = 1');
    $query->where('b.review = 0');

    if ($this->getState('search.level') == 5)
    { // City level
      $query->where('b.city = ' . (int) $this->getState('search.location', ''));
    }

    // Get the options.
    $db->setQuery($query);

    try
    {
      $locations = $db->loadObjectList();
    }
    catch (Exception $e)
    {
      // TO DO Log this.
      return false;
    }

    return $locations;
  }


  /*
   * Method to get a load of marker information based on getPropertyList
   *
   * @return array  A list of property ids and associated info
   *
   */

  public function getMapMarkers()
  {

    // The query resultset should be stored in the local model cache already
    $store = $this->getStoreId('getMapMarkers');

    // Get the info from the cache if we can
    if ($this->retrieve($store))
    {
      return $this->retrieve($store);
    }

    $db = JFactory::getDbo();

    // No data in the cache so let's get the list of markers.
    $query = $this->getListQuery($markers = true);


    $db->setQuery($query);

    try
    {

      $markers = $db->loadObjectList();
    }
    catch (Exception $e)
    {
      return false;
    }

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
  protected function store($id, $data)
  {

    // Store the data in internal cache.
    $this->cache[$id] = $data;

    $params = $this->state->get('parameters.menu');
    $lifetime = ($params) ? $params->get('cache_time', '') : 10800;
    $persistent = ($params) ? $params->get('cache', '') : false;

    // Store the data in external cache if data is persistent.
    if ($persistent)
    {
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
  public function retrieve($id, $persistent = true)
  {
    $data = null;

    // Use the internal cache if possible.
    if (isset($this->cache[$id]))
    {
      return $this->cache[$id];
    }

    $params = $this->state->get('parameters.menu', '');

    $persistent = ($params) ? $params->get('cache', '') : false;

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
  public function populateState($ordering = null, $direction = null)
  {
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

    // Determine whether we want to show only special offers or not
    $this->setState('list.offers', $input->get('offers', '', 'boolean'));

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

    if (!empty($dirn) && $dirn[0] !== '')
    {
      $sort_order = explode('_', $dirn[0]);
      $this->setState('list.sort_column', $sort_order[1]);
      $this->setState('list.direction', $sort_order[2]);
    }

    // Set the match limit.
    $this->setState('match.limit', 10000);

    // Get the rest of the filter options such as property type, facilities and activites etc.
    // populateFilterState is effectively setState as above only the input may be an array 
    $activities = $input->get('activities', '', 'array');
    $this->populateFilterState($activities, 'activities');
    $app->setUserState('list.activities', $activities);

    $property_facilities = $input->get('internal', '', 'array');
    $this->populateFilterState($property_facilities, 'property_facilities');
    $app->setUserState('list.facilities', $property_facilities);

    $external_facilities = $input->get('external', '', 'array');
    $this->populateFilterState($external_facilities, 'external_facilities');
    $app->setUserState('list.external_facilities', $external_facilities);

    $kitchen_facilities = $input->get('kitchen', '', 'array');
    $this->populateFilterState($kitchen_facilities, 'kitchen_facilities');
    $app->setUserState('list.kitchen_facilities', $kitchen_facilities);

    $suitability = $input->get('suitability', '', 'array');
    $this->populateFilterState($suitability, 'suitability');
    $app->setUserState('list.suitability', $suitability);

    // Load the parameters.
    $this->setState('params', $params);

    // Load the user state.
    $this->setState('user.id', (int) $user->get('id'));
    $this->setState('user.groups', $user->getAuthorisedViewLevels());
  }

  /*
   * Method to generate the filter state ids for later filtering in the db
   * TO DO - Modify this function to return an array of attribute 'aliases' 
   *
   */

  private function populateFilterState($input, $label)
  {

    if (is_array($input))
    {

      $ids = array();

      foreach ($input as $filter)
      {
        // Assume that this is in the form of e.g. activity_Golf_51
        // Can easily be adjusted to return alias instead of id
        $id = (int) array_pop(explode('_', $filter));

        $ids[] = $id;
      }

      $this->setState('list.' . $label, $ids);
    }
    elseif (!empty($input))
    {

      $id = (int) array_pop(explode('_', $input));

      $this->setState('list.' . $label, $id);
    }
  }

  /**
   * Adds the availability filters to the results query
   * @param JDatabaseQueryMysqli $query
   * @param type $arrival
   * @param type $departure
   * @param type $db
   * @return \JDatabaseQueryMysqli
   */
  private function getFilterAvailability(JDatabaseQueryMysqli $query, $arrival = '', $departure = '', $db = '')
  {

    if (empty($arrival) && empty($departure))
    {
      return $query;
    }

    // Join the availability table
    $query->join('left', '#__availability arr on d.unit_id = arr.unit_id');
    $query->where('arr.availability = 1');

    if ($arrival)
    {
      $query->where('arr.start_date <= ' . $db->quote($arrival));
    }

    if ($departure)
    {
      $query->where('arr.end_date >= ' . $db->quote($departure));
    }

    return $query;
  }

  /**
   * Add bedroom count filter
   * @param JDatabaseQueryMysqli $query
   * @param type $bedrooms
   * @param type $db
   * @return \JDatabaseQueryMysqli
   */
  private function getFilterBedrooms(JDatabaseQueryMysqli $query, $bedrooms = '', $db = '')
  {

    if (empty($bedrooms))
    {
      return $query;
    }

    $query->where('( single_bedrooms + double_bedrooms ) = ' . (int) $bedrooms);

    return $query;
  }

  /**
   * 
   * @param JDatabaseQueryMysqli $query
   * @param type $occupancy
   * @param type $db
   * @return \JDatabaseQueryMysqli
   */
  private function getFilterOccupancy(JDatabaseQueryMysqli $query, $occupancy = '', $db = '')
  {
    if (empty($occupancy))
    {
      return $query;
    }

    $query = $query->where('d.occupancy >= ' . (int) $occupancy);

    return $query;
  }

  /**
   * 
   * @param JDatabaseQueryMysqli $query
   * @param type $property_type
   * @param type $db
   * @return \JDatabaseQueryMysqli
   */
  private function getFilterPropertyType(JDatabaseQueryMysqli $query, $property_type = array())
  {

    if (empty($property_type))
    {
      return $query;
    }

    $types = array();

    // Get each of the property attribute id we are filtering on.
    foreach ($property_type as $type)
    {
      $ids = explode('_', $type);
      $types[] = $ids[2];
    }

    $query = $query->where('d.property_type in (' . implode(',', $types) . ')');

    return $query;
  }

  /**
   * 
   * @param JDatabaseQueryMysqli $query
   * @param type $property_type
   * @param type $db
   * @return \JDatabaseQueryMysqli
   */
  private function getFilterAccommodationType(JDatabaseQueryMysqli $query, $accommodation_type = array())
  {

    if (empty($accommodation_type))
    {
      return $query;
    }

    $types = array();

    // Get each of the property attribute id we are filtering on.
    foreach ($accommodation_type as $type)
    {
      $ids = explode('_', $type);
      $types[] = $ids[2];
    }

    $query = $query->where('d.accommodation_type in (' . implode(',', $types) . ')');

    return $query;
  }

  private function getFilterPrice(JDatabaseQueryMysqli $query, $min_price, $max_price, $db)
  {

    if (empty($min_price) && empty($max_price))
    {
      return $query;
    }

    if ($min_price)
    {

      $query = $query->where('price >= ' . (int) $min_price);
    }

    if ($max_price)
    {

      $query = $query->where('price <= ' . (int) $max_price);
    }

    return $query;
  }

  /*
   * Method to generate various attribute filter options,
   * add them to the query and then return the query object
   * TO DO - Modify this function to join on attribute rather than ID 
   * 
   * @return  query  The search query being built
   */

  private function getFilterState($filter = '', JDatabaseQueryMysqli $query, $attributes_table = '#__unit_attributes')
  {

    if (empty($filter))
    {
      return false;
    }

    if (empty($query))
    {
      return false;
    }

    // Add the activities filter to the query
    if ($this->getState('list.' . $filter, array()))
    {

      $filters = $this->getState('list.' . $filter);

      if (is_array($filters))
      {

        foreach ($filters as $key => $value)
        {
          $query->join('left', $attributes_table . ' ap' . $value . ' ON ap' . $value . '.property_id = d.unit_id');
          $query->where('ap' . $value . '.attribute_id = ' . (int) $value);
        }
      }
      else
      {
        $query->join('left', $attributes_table . ' ' . $filter . ' ON apact.property_id = d.unit_id');
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
  public function getStoreId($id = '', $page = true)
  {
    // Default will generate store IDs based on start (i.e. page number), limit (i.e. results per page), ordering
    // Possible additional things to cache against would be
    // language
    // dates
    // prices
    // facilities
    // and so on and son on
    $lang = JFactory::getLanguage()->getTag();
    if ($page)
    {
      // Add the list state for page specific data.
      $id .= ':';
      $id .= $lang;
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
      $id .= ':' . $this->getState('list.max_price');
      $id .= ':' . $this->getState('list.min_price');

      // Get each of the filter attribute id and build that into the cache key...
      $facilities = array();
      $facilities[] = $this->getState('list.activities', '');
      $facilities[] = $this->getState('list.property_facilities', '');
      $facilities[] = $this->getState('list.external_facilities', '');
      $facilities[] = $this->getState('list.kitchen_facilities', '');
      $facilities[] = $this->getState('list.property_type');
      $facilities[] = $this->getState('list.accommodation_type');

      foreach ($facilities as $key => $value)
      {

        // For the activities...
        if (is_array($value) && !empty($value))
        {
          foreach ($value as $x => $y)
          {
            $id .= ':' . $y;
            $y = '';
          }
        }
        elseif ($value)
        {
          $id .= ':' . $value;
        }
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

  protected function getCurrencyConversions()
  {

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    try
    {
      $query->select('currency, exchange_rate');
      $query->from('#__currency_conversion');

      $db->setQuery($query);

      $results = $db->loadObjectList($key = 'currency');
    }
    catch (Exception $e)
    {
      // Log this error
    }


    return $results;
  }

  /**
   * Get the path of the current search. Useful to go back up a level in the search etc
   * @return boolean
   */
  public function getCrumbs()
  {

    $db = JFactory::getDbo();
    $query = $db->getQuery();

    $id = $this->getState('search.location');
    $lang = JFactory::getLanguage()->getTag();
    $pathArr = new stdClass(); // An array to hold the paths for the breadcrumbs trail.
    // The query resultset should be stored in the local model cache already
    $store = $this->getStoreId('getCrumbs');

    // Get the info from the cache if we can
    if ($this->retrieve($store))
    {
      return $this->retrieve($store);
    }

    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');

    if ($lang == 'fr-FR')
    {
      $table = JTable::getInstance('ClassificationTranslations', 'ClassificationTable');
    }
    else
    {
      $table = JTable::getInstance('Classification', 'ClassificationTable');
    }
    $path = $table->getPath($id);
    if (!$path)
    {
      return false;
    }

    array_shift($path); // Remove the first element as it's the root of the NST
    // Put the path into a std class obj which is passed into the getPathway method.
    foreach ($path as $k => $v)
    {
      if ($v->parent_id)
      {
        $pathArr->$k->link = 'index.php?option=com_realestatesearch&Itemid=' . (int) $this->itemid . '&s_kwds=' . JApplication::stringURLSafe($v->title);
        $pathArr->$k->name = $v->title;
      }
    }

    // Push the results into cache.
    $this->store($store, $pathArr);

    // Return the path.
    return $this->retrieve($store);
  }

 

}