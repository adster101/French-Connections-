<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class AccommodationModelProperty extends JModelForm {

  /**
   * @var object item
   */
  protected $item;
  
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
	public function getForm($data = array(), $loadData = false)
	{
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
  protected function populateState() {
    $app = JFactory::getApplication();
    // Get the property id
    $id = JRequest::getInt('id');
    $this->setState('property.id', $id);

    // Load the parameters.
    $params = $app->getParams();
    $this->setState('params', $params);
    parent::populateState();
  }

  /**
   * Get the property - This should probably be using the JNested table instance for the property table...
   * 
   * @return object The message to be displayed to the user
   */
  public function getItem() {

    if (!isset($this->item)) {
      // Get the language for this request 
      $lang = & JFactory::getLanguage()->getTag();

      // Get the state for this property ID
      $id = $this->getState('property.id');

      $select = '
          hw.department, 
          toilets, 
          bathrooms,
          hw.parent_id,
          SUM( single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms ) AS bedrooms,
          single_bedrooms,
          double_bedrooms,
          triple_bedrooms,
          quad_bedrooms,
          twin_bedrooms,
          hw.id, 
          hw.created_by,
          location_details, 
          internal_facilities_other, 
          external_facilities_other, 
          activities_other, 
          getting_there, 
          hw.title, 
          description, 
          occupancy, 
          swimming, 
          distance_to_coast, 
          hw.latitude, 
          additional_price_notes, 
          linen_costs, 
          hw.longitude, 
          nearest_town,
          linen_costs,
          date_format(availability_last_updated_on, \'%D %M %Y\') as availability_last_updated_on,
          u.name,
          date_format(u.registerDate, \'%M %Y\') as advertising_since,
          ufc.phone_1,
          ufc.phone_2,
          ufc.phone_3,
          ufc.website,
          a.title as changeover_day,
          b.title as tariffs_based_on,
          c.title as base_currency,
          d.title as location_type,
          e.title as property_type,
          f.title as accommodation_type,
          g.title as swimming,
          h.title as department_as_text';

      // Language logic - essentially need to do two things, if in French
      // 1. Load the attributes_translation table in the below joins
      // 2. Load property translations for the property

      if ($lang === 'fr-FR') {
        
      }

      $this->_db->setQuery($this->_db->getQuery(true)
                      ->from('#__helloworld as hw')
                      ->select($select)
                      ->leftJoin('#__attributes a ON a.id = hw.changeover_day')
                      ->leftJoin('#__attributes b ON b.id = hw.tariff_based_on')
                      ->leftJoin('#__attributes c ON c.id = hw.base_currency')
                      ->leftJoin('#__attributes d ON d.id = hw.location_type')
                      ->leftJoin('#__attributes e ON e.id = hw.property_type')
                      ->leftJoin('#__attributes f ON f.id = hw.accommodation_type')
                      ->leftJoin('#__attributes g ON g.id = hw.swimming')
                      ->leftJoin('#__classifications h ON h.id = hw.department')
                      ->leftJoin('#__users u on hw.created_by = u.id')
                      ->leftJoin('#__user_profile_fc ufc on hw.created_by = ufc.user_id')
                      ->where('hw.id=' . (int) $id));

      if (!$this->item = $this->_db->loadObject()) {
        $this->setError($this->_db->getError());
      }
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
        $id = $this->getState('property.id');
      
        $attributes = array();
        
        // Generate a logger instance for reviews
        JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('facilities'));
        JLog::add('Retrieving facilities for - ' . $id . ')', JLog::ALL, 'facilities');
       
        $query = $this->_db->getQuery(true);
       	$query->select('a.title as attribute,at.title as attribute_type');
        $query->from('#__attributes_property ap');
        $query->join('left', '#__attributes a on a.id = ap.attribute_id');
        $query->join('left', '#__attributes_type at on at.id = a.attribute_type_id');
        $query->where('ap.property_id = '.$id);
       
        $results = $this->_db->setQuery($query)->loadObjectList();

        foreach ($results as $attribute) {
          if (!array_key_exists($attribute->attribute_type,$attributes)) {
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
  }

  /*
   * Function to return a list of units for a given property
   * 
   * 
   */

  public function getUnits() {
    if (!isset($this->units)) {

      try {
        // Get the state for this property ID
        $id = $this->getState('property.id');

        // Generate a logger instance for reviews
        JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('units'));
        JLog::add('Retrieving unit for - ' . $id . ')', JLog::ALL, 'units');

        // Load the reviews model from Property manager
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables');

        $table = JTable::getInstance('HelloWorld', 'HelloWorldTable');
        
        $leaf =  $table->isLeaf($id);
        
        
        if($leaf && $this->item->parent_id !=1) {
          $units = $table->getUnitTree($this->item->parent_id, true);
        } else {
          $units = $table->getUnitTree($id, true);
        }
               
        $this->units = $units;
        
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

      try {
        // Get the state for this property ID
        $id = $this->getState('property.id');

        // Generate a logger instance for reviews
        JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('reviews'));
        JLog::add('Retrieving reviews for - ' . $id . ')', JLog::ALL, 'reviews');

        // Load the reviews model from Property manager
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/models');

        $model = JModelLegacy::getInstance('Reviews', 'HelloWorldModel');

        // Attempt to load the reviews for this property 
        // Need to get only the published reviews
        $model->getListQuery();

        // Get the items, sweet!
        $reviews = $model->getItems();

        $this->reviews = $reviews;
        
        // Return the reviews, if any
        return $this->reviews;
      } catch (Exception $e) {
        // Log the exception and return false
        JLog::add('Problem fetching reviews for - ' . $id . $e->getMessage(), JLOG::ERROR, 'reviews');
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

    // Generate a logger instance for availability
    JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('availability'));
    JLog::add('Retrieving availability for - ' . $id . ')', JLog::ALL, 'availability');

    // First we need an instance of the availability table
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables');

    $availabilityTable = JTable::getInstance('Availability', 'HelloWorldTable', array());

    // Attempt to load the availability for this property 
    $availability = $availabilityTable->load($id);

    // Check the $availability loaded correctly
    if (!$availability) {
      // Ooops, there was a problem getting the availability
      // Check that the row actually exists
      if ($error = $availabilityTable->getError()) {
        // Fatal error
        $this->setError($error);
        return false;
      } else {
        // Not fatal error
        // Log this out to property log
        JLog::add('Problem fetching availability for - ' . $id . '(No availability?))', JLog::ERROR, 'availability');
      }
    }

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

    // First we need an instance of the availability table
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables');

    $tariffsTable = JTable::getInstance('Tariffs', 'HelloWorldTable', array());


    // Get the state for this property ID
    $id = $this->getState('property.id');

    // Attempt to load the availability for this property 
    $tariffs = $tariffsTable->load($id);

    // Check the $availability loaded correctly
    if (!$tariffs) {

      // Ooops, there was a problem getting the availability
      // Check that the row actually exists
      // Log it baby...
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
        $id = $this->getState('property.id');

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
    $parent_id = $this->item->parent_id;

    // Do some logging
    JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('images'));
    JLog::add('Retrieving images for - ' . $id . ')', JLog::ALL, 'images');

    // First we need an instance of the images table
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables');

    // Get the images depending on whether this is a parent or a child property
    if ($parent_id != 1) {

      $galleryimagesTable = JTable::getInstance('Gallery_images', 'HelloWorldTable', array());
      $images = $galleryimagesTable->load($id);
    } else {

      // Determine is this is a parent property or a leaf node...
      $propertyTable = JTable::getInstance('HelloWorld', 'HelloWorldTable', array());

      if ($propertyTable->isleaf($id)) {
        $imagesTable = JTable::getInstance('Images', 'HelloWorldTable', array());
        $images = $imagesTable->load_images($id);
      } else {
        $galleryimagesTable = JTable::getInstance('Gallery_images', 'HelloWorldTable', array());
        $images = $galleryimagesTable->load($id);
      }
    }
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
      $crumbs = $table->getPath($pk = $this->item->department);
      
      
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
	public function hit($pk = 0)
	{
		$input    = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
      // Get the property id
			$pk = ($this->item->parent_id == 1) ? $this->item->id : $this->item->parent_id;

      $db = $this->getDbo();

      $query = $db->getQuery(true);
      
      $query->insert('#__property_views');
      
      $query->columns(array('property_id','date'));
      
      $date	= JFactory::getDate()->toSql();

      $query->values("$pk, '$date'"); 
		
      $db->setQuery($query);
      
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}
  
  
}
