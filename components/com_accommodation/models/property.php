<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
 
/**
 * HelloWorld Model
 */
class AccommodationModelProperty extends JModelItem
{
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
	protected function populateState() 
	{
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
	 * Get the message
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
      
      // Language logic - should be more generic than this, in case we add more languages...
			if ($lang === 'fr-FR') {
				$select = '
          trans.title,
          sum(),
          sum(single_bedrooms+double_bedrooms+triple_bedrooms+quad_bedrooms+twin_bedrooms) as bedrooms,
          bathrooms,
          toilets,
          catid,
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
          catid, 
          toilets, 
          bathrooms, 
          SUM( single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms ) AS bedrooms, 
          hw.id, 
          location_details, 
          internal_facilities_other, 
          external_facilities_other, 
          activities_other, 
          getting_there, 
          hw.title, 
          hw.description, 
          occupancy, 
          swimming, 
          distance_to_coast, 
          latitude, 
          additional_price_notes, 
          linen_costs, 
          longitude, 
          nearest_town,
          a.title as changeover_day,
          b.title as tariffs_based_on,
          c.title as base_currency,
          d.title as location_type,
          e.title as property_type,
          f.title as accommodation_type,
          g.title as swimming';
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
				->where('hw.id='. (int)$id));

			if (!$this->item = $this->_db->loadObject()) 
			{
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
   * Function to return a list of facilities for a given property
   * 
   * 
   */
  public function getAvailability() {
 		
    // First we need an instance of the availability table
    JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_helloworld/tables');

    $availabilityTable = JTable::getInstance('Availability','HelloWorldTable', array());
    
    
    // Get the state for this property ID
		$id = $this->getState('property.id');
    
    // Attempt to load the availability for this property 
    $availability = $availabilityTable->load($id);   
    
 		// Get availability as an array of days
		$this->availability_array = HelloWorldHelper::getAvailabilityByDay( $availability );
	    
		// Build the calendar taking into account current availability...
		$this->calendar =	HelloWorldHelper::getAvailabilityCalendar($months=18, $availability = $this->availability_array);		  
    
    return $this->calendar;
  }
  
}
