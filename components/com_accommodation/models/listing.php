<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class AccommodationModelListing extends JModelForm {

  /**
   * @var object item
   */
  protected $item;

  /**
   * @var boolean review
   */
  protected $preview = false;

  public function __construct($config = array()) {

    parent::__construct($config = array());

    $input = JFactory::getApplication()->input;

    $this->preview = ($input->get('preview', 0, 'boolean')) ? true : false;
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
  public function getForm($data = array(), $loadData = false) {
    // Get the form.
    $form = $this->loadForm('com_accommodation.enquiry', 'enquiry', array('control' => 'jform', 'load_data' => true));
    if (empty($form)) {
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
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_accommodation.enquiry.data', array());

    if (empty($data)) {
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
  protected function populateState() {

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
  public function getItem() {

    if (!isset($this->item)) {
      // Get the language for this request
      $lang = & JFactory::getLanguage()->getTag();

      // Get the state for this property ID
      $id = $this->getState('property.id');

      $unit_id = $this->getState('unit.id', false);

      $select = '
        a.id,
        b.id as unit_id,
        c.title,
        c.location_details,
        c.getting_there,
        c.latitude,
        c.longitude,
        c.distance_to_coast,
        -- c.exchange_rate_eur,
        -- c.exchange_rate_usd,
        c.video_url,
        c.booking_form,
        c.deposit,
        c.security_deposit,
        c.payment_deadline,
        c.evening_meal,
        c.additional_booking_info,
        c.terms_and_conditions,
        c.first_name,
        c.surname,
        c.address,
        c.phone_1,
        c.phone_2,
        c.phone_3,
        c.city as city_id,
        k.title as changeover_day,
        d.toilets,
        d.bathrooms,
        ( single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms ) AS bedrooms, 
        d.single_bedrooms,
        d.double_bedrooms,
        d.triple_bedrooms,
        d.quad_bedrooms,
        d.twin_bedrooms,
        d.internal_facilities_other, 
        d.external_facilities_other, 
        d.activities_other, 
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
        i.title as base_currency,
        i.id as base_currency_id,
        j.title as tariffs_based_on,
        u.name,
        c.website,
        ufc.phone_1, 
        ufc.phone_2, 
        ufc.phone_3,
       	date_format(a.created_on, "%M %Y") as advertising_since';

      // Language logic - essentially need to do two things, if in French
      // 1. Load the attributes_translation table in the below joins
      // 2. Load property translations for the property

      if ($lang === 'fr-FR') {
        // 
        echo "Woot, Frenchy penchy!";
        die;
      }

      $query = $this->_db->getQuery(true);

      $query->select($select);

      $query->from('#__property as a');
      $query->leftJoin('#__unit b ON a.id = b.property_id');
      if (!$this->preview) {
        $query->leftJoin('#__property_versions c ON (c.property_id = a.id and c.id = (select max(n.id) from #__property_versions as n where n.property_id = a.id and n.review = 0))');
      } else {
        $query->leftJoin('#__property_versions c ON (c.property_id = a.id and c.id = (select max(n.id) from #__property_versions as n where n.property_id = a.id))');
      }

      if (!$this->preview) {
        $query->leftJoin('#__unit_versions d ON (d.unit_id = b.id and d.id = (select max(o.id) from #__unit_versions o where unit_id = b.id and o.review = 0))');
      } else {
        $query->leftJoin('#__unit_versions d ON (d.unit_id = b.id and d.id = (select max(o.id) from #__unit_versions o where unit_id = b.id))');
      }
      // If unit ID is specified load that unit instead of the default one
      if ($unit_id) {
        //$query->where('unit.id = ' . (int) $unit_id);
      }

      $query->leftJoin('#__classifications e ON e.id = c.city');

      // Join the property type through the property attributes table
      $query->join('left', '#__property_attributes f on (f.property_id = b.id and f.version_id = d.id)');
      $query->join('left', '#__attributes g on g.id = f.attribute_id');
      $query->where('(g.attribute_type_id = 1 or g.id is null)');

      // Join the attributes a second time to get at the accommodation type
      // This join is also based on version id to ensure we only get the version we are interested in
      $query->join('left', '#__property_attributes l on (l.property_id = b.id and l.version_id = d.id)');
      $query->join('left', '#__attributes m on m.id = l.attribute_id');
      $query->where('(m.attribute_type_id = 2 or m.id is null)');

      $query->leftJoin('#__classifications h ON h.id = c.department');
      $query->leftJoin('#__classifications n ON n.id = c.region');
      $query->leftJoin('#__attributes i ON i.id = d.base_currency');
      $query->leftJoin('#__attributes j ON j.id = d.tariff_based_on');
      $query->leftJoin('#__attributes k ON k.id = d.changeover_day');

      $query->leftJoin('#__users u on a.created_by = u.id');
      $query->leftJoin('#__user_profile_fc ufc on u.id = ufc.user_id');

      // Refine the query based on the various parameters
      $query->where('a.id=' . (int) $id);
      $query->where('b.id=' . (int) $unit_id);

      if (!$this->preview) {
        $query->where('a.published = 1');
        $query->where('b.published = 1');
      } else {
        $query->where('a.published in (0,1)');
        $query->where('b.published in (0,1)');
      }
      if (!$this->preview) {
        $query->where('a.expiry_date > ' . JFactory::getDate()->calendar('Y-m-d'));
      }
      if (!$this->item = $this->_db->setQuery($query)->loadObject()) {
        $this->setError($this->_db->getError());
      }
    }

    // Update the unit id into the model state for use later on in the model
    if (empty($unit_id)) {
      $this->setState('unit.id', $this->item->unit_id);
    }

    return $this->item;
  }

  /*
   * Function to return a list of facilities for a given property
   *
   *
   */

  public function getFacilities() {

    if (!isset($this->facilities)) {
      try {
        // Get the state for this property ID
        $unit_id = $this->getState('unit.id');

        $attributes = array();

        // Generate a logger instance for reviews
        JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('facilities'));
        JLog::add('Retrieving facilities for - ' . $unit_id . ')', JLog::ALL, 'facilities');

        $query = $this->_db->getQuery(true);
        $query->select('
            e.title as attribute,
            f.title as attribute_type
          ');
        $query->from('#__unit a');
        if (!$this->preview) {
          $query->leftJoin('#__unit_versions b ON (b.unit_id = a.id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id and c.review = 0))');
        } else {
          $query->leftJoin('#__unit_versions b ON (b.unit_id = a.id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id))');
        }

        $query->join('left', '#__property_attributes d on (d.property_id = a.id and d.version_id = b.id)');
        $query->join('left', '#__attributes e on e.id = d.attribute_id');
        $query->join('left', '#__attributes_type f on f.id = e.attribute_type_id');

        $query->where('a.id = ' . $unit_id);

        $results = $this->_db->setQuery($query)->loadObjectList();

        foreach ($results as $attribute) {
          if (!array_key_exists($attribute->attribute_type, $attributes)) {
            $attributes[$attribute->attribute_type] = array();
          }

          $attributes[$attribute->attribute_type][] = $attribute->attribute;
        }

        $this->facilities = $attributes;


        return $this->facilities;
      } catch (Exception $e) {
        // Log the exception and return false
        JLog::add('Problem fetching facilities for - ' . $unit_id . $e->getMessage(), JLOG::ERROR, 'facilities');
        return false;
      }
    }
  }

  /*
   * Function to return a list of units for a given property
   */

  public function getUnits() {

    if (!isset($this->units)) {

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
        if (!$this->preview) {

          $query->leftJoin('#__unit_versions b ON (b.unit_id = a.id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id and c.review = 0))');
        } else {
          $query->leftJoin('#__unit_versions b ON (b.unit_id = a.id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id))');
        }

        //$query->join('left', '#__unit_versions b on a.id = b.unit_id');
        $query->where('a.property_id = ' . (int) $id);
        //$query->where('a.published = 1');
        $query->order('ordering');
        if (!$this->preview) {
          $query->where('a.published = 1');
        } else {
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

  /*
   * Function to return a list of reviews for a given property
   *
   *
   */

  public function getReviews() {

    if (!isset($this->reviews)) {
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

  public function getAvailability() {
    // Get the state for this property ID
    $id = $this->getState('property.id');

    $unit_id = $this->getState('unit.id');

    // Generate a logger instance for availability
    JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('availability'));
    JLog::add('Retrieving availability for - ' . $id . ')', JLog::ALL, 'availability');

    // First we need an instance of the availability table
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/models');

    $model = JModelLegacy::getInstance('Availability', 'HelloWorldModel', array());

    // Attempt to load the availability for this property
    $availability = $model->getAvailability($unit_id);

    // Get availability as an array of days
    $this->availability_array = HelloWorldHelper::getAvailabilityByDay($availability);

    // Build the calendar taking into account current availability...
    $calendar = HelloWorldHelper::getAvailabilityCalendar($months = 18, $availability = $this->availability_array);

    return $calendar;
  }

  /*
   * Function to return a list of tariffs for a given property
   */

  public function getTariffs() {

    // Get the state for this property ID
    $unit_id = $this->getState('unit.id');

    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/models');
    $model = JModelLegacy::getInstance('Tariffs', 'HelloWorldModel');

    $tariffs = $model->getTariffs($unit_id);



    // Check the $availability loaded correctly
    if (!$tariffs) {

      $tariffs = array();
    }

    return $tariffs;
  }

  /*
   * Function to return a list of tariffs for a given property
   */

  public function getOffers() {

    if (!isset($this->offer)) {

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

  public function getImages() {

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
    if (!$this->preview) {
      $query->leftJoin('#__unit_versions b ON (b.unit_id = a.id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id and c.review = 0))');
    } else {
      $query->leftJoin('#__unit_versions b ON (b.unit_id = a.id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id))');
    }

    $query->join('left', '#__property_images_library d on (d.unit_id = a.id and d.version_id = b.id)');

    $query->where('a.id = ' . (int) $unit_id);


    $db->setQuery($query);

    $images = $db->loadObjectList();



    // Check the $availability loaded correctly
    if (!$images) {
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

  public function getCrumbs() {

    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');
    $table = JTable::getInstance('Classification', 'ClassificationTable');

    try {
      $crumbs = $table->getPath($pk = $this->item->city_id);
    } catch (Exception $e) {

      // Log the exception here...
      return false;
    }

    return $crumbs;
  }

  /**
   * Increment the hit counter for the article.
   *
   * @param	int		Optional primary key of the article to increment.
   *
   * @return	boolean	True if successful; false otherwise and internal error set.
   */
  public function hit() {

    $input = JFactory::getApplication()->input;
    $hitcount = $input->getInt('hitcount', 1);

    if ($hitcount) {
      // Get the property id
      $pk = $this->getState('unit.id', false);

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
}
