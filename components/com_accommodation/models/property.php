<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');


/**
 * HelloWorld Model
 */
class AccommodationModelProperty extends JModelItem {

  /**
   * @var object item
   */
  protected $item;

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
    // Get the message id
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
          
      // Language logic - should be more generic than this, in case we add more languages...
      if ($lang === 'fr-FR') {
        $select = '
          trans.title,
          sum(),
          sum(single_bedrooms+double_bedrooms+triple_bedrooms+quad_bedrooms+twin_bedrooms) as bedrooms,
          bathrooms,
          toilets,
          department,
          hel.id,
          location_details,
          internal_facilities_other,
          external_facilities_other,
          activities_other,
          getting_there,
          trans.description,
          distance_to_coast,
          occupancy,
          swimming,
          latitude,
          longitude,
          linen_costs,
          additional_price_notes,
          nearest_town';
      } else {
        $select = '
          hw.department, 
          toilets, 
          bathrooms,
          hw.parent_id,
          SUM( single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms ) AS bedrooms, 
          hw.id, 
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
          a.title as changeover_day,
          b.title as tariffs_based_on,
          c.title as base_currency,
          d.title as location_type,
          e.title as property_type,
          f.title as accommodation_type,
          g.title as swimming,
          h.title as department_as_text';
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
    if (!isset($this->item))
    // Get the language for this request 
      $lang = & JFactory::getLanguage()->getTag();
    // Get the state for this property ID
    $id = $this->getState('property.id');
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
    JLog::add('Retrieving availability for - ' . $id . ')', JLog::ERROR, 'import_images');
 
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
        //$this->setError(JText::sprintf('COM_ACCOMMODATION_ERROR_GETTING_AVAILABILITY', $id));
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
    JLog::add('Retrieving images for - ' . $id . ')', JLog::ERROR, 'import_images');    

    // First we need an instance of the images table
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables');    

    // Get the images depending on whether this is a parent or a child property
    if ($parent_id !=1) { 
     
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

  public function getCrumbs( ) {
    
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');
    $table = JTable::getInstance('Classification', 'ClassificationTable');
    
    try {
      $crumbs = $table->getPath($pk=$this->item->department);
      
    } catch (Exception $e) {

      // Log the exception here...
      return false;
      
    }

    return $crumbs;
    
  }
}
