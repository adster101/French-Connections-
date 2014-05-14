<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class AccommodationModelListing extends JModelForm
{

  /**
   * @var object item
   */
  protected $item;

  /**
   * @var boolean review
   */
  protected $preview = false;

  public function __construct($config = array())
  {

    parent::__construct($config = array());

    $input = JFactory::getApplication()->input;

    $this->preview = ($input->get('preview', 0, 'boolean')) ? true : false;
  }

  public function getShortlist()
  {

    // Get an instance of the shortlist model
    JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_shortlist/models');
    $model = JModelLegacy::getInstance('Shortlist', 'ShortlistModel');

    $user = JFactory::getUser();
    $user_id = $user->id;

    $shortlist = $model->getShortlist($user_id);

    return $shortlist;
  }

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Enquiry', $prefix = 'EnquiriesTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Method to get the contact form.
   *
   * The base form is loaded from XML and then an event is fired
   *
   *
   * @param	array	$data		An optional array of data for the form to interrogate.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	JForm	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = true)
  {

    // Get the form.
    $form = $this->loadForm('com_accommodation.enquiry', 'enquiry', array('control' => 'jform', 'load_data' => $loadData));

    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData()
  {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_accommodation.enquiry.data', array());

    if (empty($data))
    {
      $data = $this->getItem();
    }

    return $data;
  }

  /**
   * Method to auto-populate the model state.
   *
   * This method should only be called once per instantiation and is designed
   * to be called on the first call to the getState() method unless the model
   * configuration flag to ignore the request is set.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @return	void
   * @since	1.6
   */
  protected function populateState()
  {

    // Get the input values etc
    $app = JFactory::getApplication();
    $input = $app->input;


    // Get the property id
    $id = $input->get('id', '', 'int');

    // Get the unit id
    $unit_id = $input->get('unit_id', '', 'int');

    // Set the states
    $this->setState('property.id', $id);

    $this->setState('unit.id', $unit_id);

    // Load the parameters.
    $params = $app->getParams();
    $this->setState('params', $params);
    parent::populateState();
  }

  /**
   * Get the property listing details. This comprises of the main property and the unit. If no unit specified the first based on unit ordering is used...
   *
   * @return object The message to be displayed to the user
   */
  public function getItem()
  {

    if (!isset($this->item))
    {
      // Get the language for this request
      $lang = & JFactory::getLanguage()->getTag();

      // Get the state for this property ID
      $id = $this->getState('property.id');

      $unit_id = $this->getState('unit.id', false);

      $select = '
        a.id as property_id,
        a.sms_alert_number,
        a.sms_valid,
        a.sms_nightwatchman,
        b.id as unit_id,
        c.location_details,
        c.local_amenities,
        c.getting_there,
        c.use_invoice_details,
        c.latitude,
        c.longitude,
        c.distance_to_coast,
        c.video_url,
        c.booking_form,
        c.deposit,
        c.security_deposit,
        c.payment_deadline,
        c.evening_meal,
        c.additional_booking_info,
        c.deposit_currency,
        c.terms_and_conditions,
        c.first_name,
        c.surname,
        c.address,
        c.email_1,
        c.email_2,
        c.phone_1,
        c.phone_2,
        c.phone_3,
        c.city as city_id,
        c.languages_spoken,
        k.title as changeover_day,
        d.toilets,
        d.bathrooms,
        (single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms) AS bedrooms, 
        d.single_bedrooms,
        d.double_bedrooms,
        d.triple_bedrooms,
        d.quad_bedrooms,
        d.twin_bedrooms,
        d.occupancy,
        d.additional_price_notes,
        d.linen_costs,
        date_format(b.availability_last_updated_on, "%D %M %Y") as availability_last_updated_on, 
        d.unit_title,
        d.description,
        e.title as city,
        n.title as region,
        g.title as property_type,
        m.title as accommodation_type,
        h.title as department,
        d.base_currency,
        j.title as tariffs_based_on,
        u.name,
        u.email,
        c.website,
        air.name as airport,
        air.code as airport_code,
        air.id as airport_id,
        ufc.phone_1, 
        ufc.phone_2, 
        ufc.phone_3,
        ufc.firstname,
        ufc.surname,
        ufc.exchange_rate_eur,
        ufc.exchange_rate_usd,
        ufc.address1,
        ufc.address2,
        ufc.city as owner_city,
        ufc.region as county,
        ufc.country,
        ufc.postal_code,
       	date_format(a.created_on, "%M %Y") as advertising_since';

      $query = $this->_db->getQuery(true);

      $query->select($select);

      $query->from('#__property as a');
      $query->leftJoin('#__unit b ON a.id = b.property_id');
      if (!$this->preview)
      {
        $query->leftJoin('#__property_versions c ON (c.property_id = a.id and c.id = (select max(n.id) from #__property_versions as n where n.property_id = a.id and n.review = 0))');
      }
      else
      {
        $query->leftJoin('#__property_versions c ON (c.property_id = a.id and c.id = (select max(n.id) from #__property_versions as n where n.property_id = a.id))');
      }

      if (!$this->preview)
      {
        $query->leftJoin('#__unit_versions d ON (d.unit_id = b.id and d.id = (select max(o.id) from #__unit_versions o where unit_id = b.id and o.review = 0))');
      }
      else
      {
        $query->leftJoin('#__unit_versions d ON (d.unit_id = b.id and d.id = (select max(o.id) from #__unit_versions o where unit_id = b.id))');
      }

      // Join the translations table to pick up any translations 
      if ($lang === 'fr-FR')
      {

        $query->select('p.unit_title, p.description, p.additional_price_notes, p.linen_costs');
        $query->join('left', '#__unit_versions_translations p on p.version_id = d.id');

        $query->select('q.getting_there, q.location_details');
        $query->join('left', '#__property_versions_translations q on q.version_id = c.id');

        $query->leftJoin('#__classifications_translations e ON e.id = c.city');

        // Join the property type through the property attributes table
        $query->join('left', '#__attributes_translation g on g.id = d.property_type');

        // Join the attributes a second time to get at the accommodation type
        // This join is also based on version id to ensure we only get the version we are interested in
        $query->join('left', '#__attributes_translation m on m.id = d.accommodation_type');
        $query->leftJoin('#__classifications_translations h ON h.id = c.department');
        $query->leftJoin('#__classifications_translations n ON n.id = c.region');
        $query->leftJoin('#__attributes_translation j ON j.id = d.tariff_based_on');
        $query->leftJoin('#__attributes_translation k ON k.id = d.changeover_day');
      }
      else
      {
        $query->leftJoin('#__classifications e ON e.id = c.city');

        // Join the property type through the property attributes table
        $query->join('left', '#__attributes g on g.id = d.property_type');

        // Join the attributes a second time to get at the accommodation type
        // This join is also based on version id to ensure we only get the version we are interested in
        $query->join('left', '#__attributes m on m.id = d.accommodation_type');
        $query->leftJoin('#__classifications h ON h.id = c.department');
        $query->leftJoin('#__classifications n ON n.id = c.region');
        $query->leftJoin('#__attributes j ON j.id = d.tariff_based_on');
        $query->leftJoin('#__attributes k ON k.id = d.changeover_day');
      }


      $query->leftJoin('#__users u on a.created_by = u.id');
      $query->leftJoin('#__user_profile_fc ufc on u.id = ufc.user_id');

      $query->leftJoin('#__airports air on air.id = c.airport');

      // Refine the query based on the various parameters
      $query->where('a.id=' . (int) $id);
      $query->where('b.id=' . (int) $unit_id);

      if (!$this->preview)
      {
        $query->where('c.review = 0');
        $query->where('d.review = 0');
      }
      else
      {
        $query->where('c.review in (0,1)');
        $query->where('d.review in (0,1)');
      }

      if (!$this->preview)
      {
        // TO DO: We should check the expiry date at some point.
        $query->where('a.expiry_date >= ' . $this->_db->quote(JFactory::getDate()->calendar('Y-m-d')));
      }

      try {

        $this->item = $this->_db->setQuery($query)->loadObject();
      } catch (Exception $e) {
        // Runtime exception
        // Different to a null result.
        // TO DO - Log me baby
      }

      if (empty($this->item))
      {
        // This property has expired or is otherwise unavailable.                
        return false;
      }
    }

    // Update the unit id into the model state for use later on in the model
    if (empty($unit_id))
    {
      $this->setState('unit.id', $this->item->unit_id);
    }

    if (!empty($this->item->city))
    {
      $this->item->city = trim(preg_replace('/\(.*?\)/', '', $this->item->city));
    }


    return $this->item;
  }

  /**
   * Function to get maps items to show on the location map.
   * At present, is only 'places of interest'
   * 
   */
  public function getMapItems($lat = '', $lon = '')
  {

    $db = JFactory::getDbo();
    $app = JFactory::getApplication('site');
    $menus = $app->getMenu();
    $items = $menus->getItems('component', 'com_placeofinterest');
    $items = is_array($items) ? $items : array();
    $itemid = $items[0]->id;
    $query = $db->getQuery(true);

    $query->select("id, left(description, 125) as description, title, latitude, longitude, alias");

    $query->from('#__places_of_interest a');

    $query->where('
        ( 3959 * acos(cos(radians(' . $lat . ')) *
          cos(radians(a.latitude)) *
          cos(radians(a.longitude) - radians(' . $lon . '))
          + sin(radians(' . $lat . '))
          * sin(radians(a.latitude))) < 50)
        ');

    $db->setQuery($query);
    $rows = $db->loadObjectList();
    foreach ($rows as $k => $v) {
      $rows[$k]->description = JHtml::_('string.truncate', $v->description, 75, true, false);
      $rows[$k]->link = JRoute::_('index.php?option=com_placeofinterest&Itemid=' . $itemid . '&place=' . $v->alias);
    }

    return $rows;
  }

  /**
   * Couple of functions to return the faciliies for unit and property
   * Required 'cos the native union bit in joomla doesn't work.
   * 
   * @return type
   * 
   */
  public function getUnitFacilities()
  {

    return $this->getFacilities('#__unit', '#__unit_versions', 'unit_id', '#__unit_attributes');
  }

  public function getPropertyFacilities()
  {

    return $this->getFacilities('#__property', '#__property_versions', 'property_id', '#__property_attributes');
  }

  /*
   * Function to return a list of facilities for a given property
   *
   *
   */

  public function getFacilities($table1 = '', $table2 = '', $field = '', $table3 = '')
  {

    $lang = JFactory::getLanguage()->getTag();




    try {

      // Get the state for this property ID
      $unit_id = $this->getState('unit.id');
      $property_id = $this->getState('property.id');

      $id = ($field == 'unit_id') ? $unit_id : $property_id;

      $attributes = array();

      // Generate a logger instance for reviews
      JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('facilities'));
      JLog::add('Retrieving facilities for - ' . $id . ')', JLog::ALL, 'facilities');

      $query = $this->_db->getQuery(true);
      $query->select('
            e.title as attribute,
            f.title as attribute_type
          ');
      $query->from($table1 . ' a');
      if (!$this->preview)
      {
        $query->leftJoin($table2 . ' b ON (b.' . $field . ' = a.id and b.id = (select max(c.id) from ' . $table2 . ' c where ' . $field . ' = a.id and c.review = 0))');
      }
      else
      {
        $query->leftJoin($table2 . ' b ON (b.' . $field . ' = a.id and b.id = (select max(c.id) from ' . $table2 . ' c where ' . $field . ' = a.id))');
      }

      $query->join('left', $table3 . ' d on (d.property_id = a.id and d.version_id = b.id)');
      if ($lang === 'fr-FR')
      {
        $query->join('left', '#__attributes_translation e on e.id = d.attribute_id');
      }
      else
      {
        $query->join('left', '#__attributes e on e.id = d.attribute_id');
      }
      $query->join('left', '#__attributes_type f on f.id = e.attribute_type_id');

      $query->where('a.id = ' . (int) $id);

      $results = $this->_db->setQuery($query)->loadObjectList();

      foreach ($results as $attribute) {
        if (!array_key_exists($attribute->attribute_type, $attributes))
        {
          $attributes[$attribute->attribute_type] = array();
        }

        $attributes[$attribute->attribute_type][] = $attribute->attribute;
      }

      $this->facilities = $attributes;


      return $this->facilities;
    } catch (Exception $e) {
      // Log the exception and return false
      JLog::add('Problem fetching facilities for - ' . $id . $e->getMessage(), JLOG::ERROR, 'facilities');
      return false;
    }
  }

  /*
   * Function to return a list of units for a given property
   */

  public function getUnits()
  {

    if (!isset($this->units))
    {

      try {
        // Get the state for this property ID
        $id = $this->getState('property.id');

        // Generate a logger instance for reviews
        JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('units'));
        JLog::add('Retrieving units for - ' . $id . ')', JLog::ALL, 'units');

        // Get the node and children as a tree.
        $query = $this->_db->getQuery(true);
        $select = 'unit_title,a.id,occupancy,a.property_id,(single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms) as bedrooms';
        $query->select($select);
        $query->from('#__unit a');
        if (!$this->preview)
        {

          $query->leftJoin('#__unit_versions b ON (b.unit_id = a.id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id and c.review = 0))');
        }
        else
        {
          $query->leftJoin('#__unit_versions b ON (b.unit_id = a.id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id))');
        }

        //$query->join('left', '#__unit_versions b on a.id = b.unit_id');
        $query->where('a.property_id = ' . (int) $id);
        //$query->where('a.published = 1');
        $query->order('ordering');
        if (!$this->preview)
        {
          $query->where('a.published = 1');
        }
        else
        {
          $query->where('a.published in (0,1)');
        }
        return $this->_db->setQuery($query)->loadObjectList();

        return $this->units;
      } catch (Exception $e) {
        // Log the exception and return false
        JLog::add('Problem fetching units for - ' . $id . $e->getMessage(), JLOG::ERROR, 'units');
        return false;
      }
    }
  }

  /**
   * Gets a list of related properties based on the property someone has just enquired on.
   * 
   * @return boolean
   */
  public function getRelatedProps()
  {

    if (empty($this->item))
    {
      return false;
    }

    JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_fcsearch/models');

    $app = JFactory::getApplication();

    $input = $app->input;
    $filter = JFilterInput::getInstance();

    $location = JApplication::stringURLSafe($filter->clean($this->item->department, 'string'));

    if (!$location)
    {
      return false;
    }

    // Set s_kwds in the input data. E.g. spoof a location search...
    $app->input->set('s_kwds', $location);
    $app->input->set('limit', 4);

    $model = JModelLegacy::getInstance('Search', 'FcSearchModel');

    $model->getLocalInfo(); // Must call this first, probably should be a protected method called internally from the model
    $results = $model->getResults(); // Get the property listings, related to this one, if any.s

    return $results;
  }

  /*
   * Function to return a list of reviews for a given property
   *
   *
   */

  public function getReviews()
  {

    if (!isset($this->reviews))
    {
      $unit_id = $this->getState('unit.id');

      try {
        // Get the state for this property ID
        // Generate a logger instance for reviews
        JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('reviews'));
        JLog::add('Retrieving reviews for - ' . $unit_id . ')', JLog::ALL, 'reviews');

        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Select some fields
        $query->select('*');

        // From the special offers table
        $query->from('#__reviews as a');

        // Only want those assigned to the current property
        $query->where('unit_id = ' . $unit_id);

        $db->setQuery($query);

        $reviews = $db->loadObjectList();

        $this->reviews = $reviews;

        // Return the reviews, if any
        return $this->reviews;
      } catch (Exception $e) {
        // Log the exception and return false
        JLog::add('Problem fetching reviews for - ' . $unit_id . $e->getMessage(), JLOG::ERROR, 'reviews');
        return false;
      }
    }
  }

  /*
   * Function to return availability calendar for a given property
   *
   *
   */

  public function getAvailability()
  {
    // Get the state for this property ID
    $id = $this->getState('property.id');

    $unit_id = $this->getState('unit.id');

    // Generate a logger instance for availability
    JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('availability'));
    JLog::add('Retrieving availability for - ' . $id . ')', JLog::ALL, 'availability');

    // First we need an instance of the availability table
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');

    $model = JModelLegacy::getInstance('Availability', 'RentalModel', array());

    // Attempt to load the availability for this property
    $availability = $model->getAvailability($unit_id);

    // Get availability as an array of days
    $this->availability_array = RentalHelper::getAvailabilityByDay($availability);

    // Build the calendar taking into account current availability...
    $calendar = RentalHelper::getAvailabilityCalendar($months = 18, $availability = $this->availability_array, $link = false);

    return $calendar;
  }

  /*
   * Function to return a list of tariffs for a given property
   */

  public function getTariffs()
  {

    // Get the state for this property ID
    $unit_id = $this->getState('unit.id');

    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');
    $model = JModelLegacy::getInstance('Tariffs', 'RentalModel');

    $tariffs = $model->getTariffs($unit_id);



    // Check the $availability loaded correctly
    if (!$tariffs)
    {

      $tariffs = array();
    }

    return $tariffs;
  }

  /*
   * Function to return a list of tariffs for a given property
   */

  public function getOffers()
  {

    if (!isset($this->offer))
    {

      try {
        // Get the state for this property ID
        $id = $this->getState('unit.id', '');

        // Generate a logger instance for reviews
        JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('offers'));
        JLog::add('Retrieving special offers for - ' . $id . ')', JLog::ALL, 'offers');

        // Get a special offers table instance
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_specialoffers/tables');

        $table = JTable::getInstance('SpecialOffer', 'SpecialOffersTable');

        $offer = $table->getOffer($id);

        $this->offer = $offer;

        // Return the offer, if any

        return $this->offer;
      } catch (Exception $e) {
        // Log the exception and return false
        JLog::add('Problem fetching reviews for - ' . $id . $e->getMessage(), JLOG::ERROR, 'reviews');
        return false;
      }
    }
  }

  /*
   * Function to get a list of images for a property
   *
   */

  public function getImages()
  {

    // Get the property ID
    $id = $this->getState('property.id');

    // Get the state for this property ID
    $unit_id = $this->getState('unit.id');

    $app = JFactory::getApplication();


    // Do some logging
    JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('images'));
    JLog::add('Retrieving images for - ' . $unit_id . ')', JLog::ALL, 'images');

    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Get a list of the images uploaded against this listing
    $query->select('
      d.unit_id,
      d.image_file_name,
      d.caption,
      d.ordering
    ');

    $query->from('#__unit a');
    if (!$this->preview)
    {
      $query->leftJoin('#__unit_versions b ON (b.unit_id = a.id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id and c.review = 0))');
    }
    else
    {
      $query->leftJoin('#__unit_versions b ON (b.unit_id = a.id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id))');
    }

    $query->join('left', '#__property_images_library d on (d.unit_id = a.id and d.version_id = b.id)');

    $query->where('a.id = ' . (int) $unit_id);


    $db->setQuery($query);

    $images = $db->loadObjectList();



    // Check the $availability loaded correctly
    if (!$images)
    {
      // Ooops, there was a problem getting the availability
      // Check that the row actually exists
      JLog::add('Problem fetching images for - ' . $id, JLog::ERROR, 'images');

      // Log it baby...
    }

    return $images;
  }

  /*
   * Function to return the location breadcrumb trail for a property
   *
   */

  public function getCrumbs()
  {

    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');
    $table = JTable::getInstance('Classification', 'ClassificationTable');
    $pathArr = new stdClass(); // An array to hold the paths for the breadcrumbs trail.

    try {
      $path = $table->getPath($pk = $this->item->city_id);
    } catch (Exception $e) {

      // Log the exception here...
      return false;
    }

    array_shift($path); // Remove the first element as it's the root of the NST
    // Put the path into a std class obj which is passed into the getPathway method.
    foreach ($path as $k => $v) {
      if ($v->parent_id)
      {
        $pathArr->$k->link = 'index.php?option=com_fcsearch&Itemid=165&s_kwds=' . JApplication::stringURLSafe($v->title);
        $pathArr->$k->name = $v->title;
      }
    }

    // Add the PRN as the final element
    $total = count($path);
    $pathArr->$total->link = '';
    $pathArr->$total->name = JText::sprintf('COM_ACCOMMODATION_PROPERTY_REFERENCE', (int) $this->item->property_id);

    return $pathArr;
  }

  /**
   * Increment the hit counter for the article.
   *
   * @param	int		Optional primary key of the article to increment.
   *
   * @return	boolean	True if successful; false otherwise and internal error set.
   */
  public function hit()
  {

    $input = JFactory::getApplication()->input;
    $hitcount = $input->getInt('hitcount', 1);

    if ($hitcount)
    {
      // Get the property id
      $pk = $this->getState('property.id', false);

      $db = $this->getDbo();

      $query = $db->getQuery(true);

      $query->insert('#__property_views');

      $query->columns(array('property_id', 'date_created'));

      $date = JFactory::getDate()->toSql();

      $query->values("$pk, '$date'");

      $db->setQuery($query);

      try {
        $db->execute();
      } catch (RuntimeException $e) {
        $this->setError($e->getMessage());
        return false;
      }
    }
    return true;
  }

  /**
   * Function to process and send an enquiry onto an owner...
   * 
   * Also need to send an email to the holiday maker as an acknowledgement
   * 
   * Filter the message based on the banned text phrases and banned email addresses. This seems futile.
   * How easy is it to generate a new email address or alter the phrasing. 
   * More robust would be to require a user account, keep a track of how many email a user is sending in a 
   * certain timeframe and trap any for manual review above that number. Similar to what happens now. 
   * 
   * @param array $data
   * @param type $params
   * @return boolean
   */
  public function processEnquiry($data = array(), $params = '', $id = '', $unit_id = '')
  {

    // Set up the variables we need to process this enquiry
    $app = JFactory::getApplication();
    $date = JFactory::getDate();
    $owner_email = '';
    $owner_name = '';
    $valid = true;
    jimport('clickatell.SendSMS');
    $sms_params = JComponentHelper::getParams('com_rental');
    $banned_emails = explode(',', $params->get('banned_email'));
    $banned_phrases = explode(',', $params->get('banned_phrases'));
    // The details of where who is sending the email (e.g. FC in this case).
    $mailfrom = $app->getCfg('mailfrom');
    $fromname = $app->getCfg('fromname');
    $sitename = $app->getCfg('sitename');
    $minutes_until_safe_to_send = '';

    // Check the banned email list 
    if (in_array($data['guest_email'], $banned_emails))
    {
      $valid = false; // Naughty!
    }

    // Check the banned phrases list - This is currently done as a form field validation rule
    if (!$this->contains($data['message'], $banned_phrases, true))
    {
      $valid = false; // Naughty!
    }

    // Add enquiries and property manager table paths
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_enquiries/tables');

    $table = $this->getTable();

    // Set the date created timestamp
    $data['date_created'] = $date->toSql();
    $data['property_id'] = $id;
    $data['unit_id'] = $unit_id;

    if (!$valid)
    {
      $data['state'] = -1;
    }

    // Check that we can save the data and save it out to the enquiry table
    if (!$table->save($data))
    {
      return false;
    }

    // We only need to process the rest of this is the enquiry is validated
    if ($valid)
    {
      // Need to get the contact detail preferences for this property/user combo
      $item = $this->getItem();

      // If the property is set to use invoice details
      // Override anything set in the property version 
      if ($item->use_invoice_details)
      {

        $owner_email = (JDEBUG) ? $params->get('admin_enquiry_email') : $item->email;
        // This assumes that name is in synch with the user profile table first and last name fields...
        $owner_name = htmlspecialchars($item->name);
      }
      else
      {
        // We just use the details from the contact page, possibly also send this to the owner...
        $owner_email = (JDEBUG) ? $params->get('admin_enquiry_email') : $item->email_1;
        $owner_name = htmlspecialchars($item->firstname) . ' ' . htmlspecialchars($item->surname);
      }

      // The details of the enquiry as submitted by the holiday maker
      $firstname = $data['guest_forename'];
      $surname = $data['guest_surname'];
      $email = $data['guest_email'];
      $phone = $data['guest_phone'];
      $message = $data['message'];
      $arrival = $data['start_date'];
      $end = $data['end_date'];
      $adults = $data['adults'];
      $children = $data['children'];
      $full_name = $firstname . ' ' . $surname;

      // Prepare email body
      $body = JText::sprintf($params->get('owner_email_enquiry_template'), $owner_name, $firstname, $surname, $email, $phone, htmlspecialchars($message, ENT_COMPAT, 'UTF-8'), $arrival, $end, $adults, $children);

      $mail = JFactory::getMailer();
      $mail->addRecipient($owner_email, $owner_name);
      $mail->addReplyTo(array($mailfrom, $fromname));
      $mail->setSender(array($mailfrom, $fromname));
      $mail->addBCC($mailfrom, $fromname);
      $mail->setSubject($sitename . ': ' . JText::sprintf('COM_ACCOMMODATION_NEW_ENQUIRY_RECEIVED', $item->unit_title, $id));
      $mail->setBody($body);

      if (!$mail->Send())
      {
        return false;
      }

      // Prepare email body for the holidaymaker email
      $body = JText::sprintf($params->get('holiday_maker_email_enquiry_template'), $firstname);
      $mail->ClearAllRecipients();
      $mail->ClearAddresses();
      $mail->setBody($body);
      $mail->setSubject(JText::sprintf('COM_ACCOMMODATION_NEW_ENQUIRY_SENT', $item->unit_title));
      $mail->addRecipient($email);

      if (!$mail->Send())
      {
        return false;
      }

      // Only fire up the SMS bit if the owner is subscribed to SMS alerts...
      if ($item->sms_valid)
      {

        $sms = new SendSMS($sms_params->get('username'), $sms_params->get('password'), $sms_params->get('id'));
        /*
         *  if the login return 0, means that login failed, you cant send sms after this 
         */
        if (!$sms->login())
        {
          return false;
        }

        // Set default timezone so we can work out the correct time now
        date_default_timezone_set("Europe/London");

        // Get the time in 24h format with minutes
        $time = date('Hi');

        if ($item->sms_nightwatchman && ((int) $time > 2200 || $time < 0800))
        {

          // Must be 'night' time
          // Determine the number of minutes until 0800h when it's safe to send the SMS
          if ($time < 2359)
          {
            // Get the unix timestamp for tomorrow at 0800h
            $tomorrow_at_eight = mktime(8, 0, 0, date('m'), date('d') + 1, date('y'));

            // Calculate the minutes between now and when we it's safe to send the message.
            $minutes_until_safe_to_send = round(($tomorrow_at_eight - time()) / 60);
          }
          else
          {
            // Get the unix timestamp for later today at 0800h
            $today_at_eight = mktime(8, 0, 0, date('m'), date('d'), date('y'));

            // Calculate the minutes between now and when we it's safe to send the message.
            $minutes_until_safe_to_send = round(($today_at_eight - time()) / 60);
          }
        }

        /*
         * Send sms using the simple send() call 
         */
        if (!$sms->send($item->sms_alert_number, JText::sprintf('COM_ACCOMMODATION_NEW_ENQUIRY_RECEIVED_SMS_ALERT', $id, $full_name, $phone, $email), $minutes_until_safe_to_send))
        {
          return false;
        }
      }
    }

    // We are done.
    // TO DO: Should add some logging of the different failure points above.
    return true;
  }

  /**
   * Function uses a PCRE to search a string for a series of banned phrases. 
   * http://stackoverflow.com/questions/6228581/how-to-search-array-of-string-in-another-string-in-php
   * Fast? 
   * 
   * @param type $string
   * @param array $search
   * @param type $caseInsensitive
   * @return type
   */
  function contains($string, Array $search, $caseInsensitive = false)
  {
    $exp = '/'
            . implode('|', array_map('preg_quote', $search))
            . ($caseInsensitive ? '/i' : '/');
    return preg_match($exp, $string) ? true : false;
  }

}
