<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.tablenested');

// import the model helper lib
jimport('joomla.application.component.model');

/**
 * Hello Table class
 */
class HelloWorldTableHelloWorld extends JTableNested
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__helloworld', 'id', $db);
	}
	
	/**
	 * Overloaded bind function
         *
	 * @param       array           named array
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = '') 
	{		
    
    // Bind the rules.
    if (isset($array['rules']) && is_array($array['rules']))
    {
            $rules = new JAccessRules($array['rules']);
            $this->setRules($rules);
    }    
    
    // Merge and bind the params
		if (isset($array['params']) && is_array($array['params']) && $this->params) 
		{
			// $this is an instance of HelloWorldTableHelloWorld (and includes a copy of the record as it stands in the db)
			// Loop over the $array['params']
			// For each check that this key isn't already stored as attribute
			// If it is delete it as there is a new one incoming
			// merge the two sets of data
			// so that both sets of params are preserved
			foreach($array['params'] as $key=>$value) {
				if ($this->params[$key] !== null) {
					$tmp = $this->params[$key] = '';
				}
			}			
      //print_r($this->params);die;
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$parameter->merge($this->params);
			$array['params'] = (string)$parameter;
		}
		return parent::bind($array, $ignore);
	}
 
	/**
	 * Overloaded load function
	 *
	 * @param       int $pk primary key
	 * @param       boolean $reset reset data
	 * @return      boolean
	 * @see JTable:load
	 */
	public function load($pk = null, $reset = true, $lang = '') 
	{		
		if (parent::load($pk, $reset)) 
		{
			// Get the current editing language for this property, if it's not passed in
      if (empty($lang)) {
        $lang = HelloWorldHelper::getLang();	
      }
			// Need to load any translations here if the editing language different from the property language
			$this->loadPropertyTranslation($lang);
      
			return true;
		}
		else
		{
			return false;
		}
	}
  
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_helloworld.message.'.(int) $this->$k;
	}
 
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}
 
	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 * @since	1.6
	 */
	protected function _getAssetParentId()
	{
    // We will retrieve the parent-asset from the Asset-table
    $assetParent = JTable::getInstance('Asset');

    // Default: if no asset-parent can be found we take the global asset
    $assetParentId = $assetParent->getRootId();

    // Find the parent-asset
    // Compared to the MVC tut and the classification component 
    // we aren't testing whether this content is categorised or not.
    // This is because we're not anticipating that the content will be
    // categoriesed using the built in joomla category component
   
    // The item has the component as asset-parent
    $assetParent->loadByName('com_helloworld');
    
    // Return the found asset-parent-id
    if ($assetParent->id)
    {
      $assetParentId=$assetParent->id;
    }

    return $assetParentId;
	}

	/**
	 * Stores a property
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{
		// Get the post data, mainly in case availability or tariff data is set.
		// For availability would it be cleaner to move changeover day to tariffs?
		// Maybe although we need to track availability last updated on...against the accommodation unit.
		$POST = JRequest::getVar('jform', $default = array() );
    
    // Transform the params field
		if (is_array($this->params)) {
			$registry = new JRegistry();
			$registry->loadArray($this->params);
			$this->params = (string)$registry;
		}
    
		// Get the current editing language for this property
		$lang = HelloWorldHelper::getLang();
		
		// TO DO: Determine if this is a 'translation' - for now, determine this is the case if the editing language is fr-FR
		$this->savePropertyTranslation($lang);
		
    // Save the facilities and suitability options
    $this->savePropertyAttributes($POST);
    
    // Save the tariff details. Pass $POST to save function to determine if we have any or not
    $this->savePropertyTariffs($POST);
    
    // Save the image details, if any are passed in, baby
    $this->saveImageDetails($POST);
    
		// Do we have availability data to update? TO DO - Wrap this in a function (perhaps in the controller?)
		if (isset($POST['start_date']) && isset($POST['end_date']) && isset($POST['availability'])) { // We have some new availability?
      
      // TO DO: Tidy this up a bit - new method?
      // E.g. $this->saveAvailability();
      $start_date = $POST['start_date'];
      $end_date = $POST['end_date'];
      $availability_status = $POST['availability'];
      
      if ($start_date && $end_date) {
      
        $availabilityTable = JTable::getInstance($type = 'Availability', $prefix = 'HelloWorldTable', $config = array());
        $availability = $availabilityTable->load($this->id);

        $availability_by_day = HelloWorldHelper::getAvailabilityByDay($availability, $start_date, $end_date, $availability_status);
        $availability_by_period = HelloWorldHelper::getAvailabilityByPeriod($availability_by_day);

        // Delete existing availability
        // Need to wrap this in some logic
        $availabilityTable->delete($this->id);

        // Bind the translated fields to the JTable instance	
        if (!$availabilityTable->save($this->id, $availability_by_period))
        {
          JError::raiseWarning(500, $availabilityTable->getError());
          return false;
        }	else {
          // Update the availability last updated on field
          $this->availability_last_updated_on = JFactory::getDate()->toSql();
        }
      }

		}
				
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		
		if ($this->id) {
			// Existing item
			$this->modified		= $date->toSql();
			$this->modified_by	= $user->get('id');
		} else {
			// New newsfeed. A feed created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			
			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');

      }
    		     
      $this->setLocation($this->parent_id, 'last-child');


    }

		$this->debug(1);

		// Attempt to store the data.
		return parent::store($updateNulls);
	}

  /*
   * savePropertyAttributes - Method to check the attributes supplied for a property and save them 
   * 
   * param @$POST - The post data containing the attributes 
   * 
   */
  protected function savePropertyAttributes( $POST=array() )
  {
    if(!array($POST)) {
      return true;
    }
    
    // Check for a tariffs array in the POST structure
    if (!array_key_exists('facilities', $POST)) {
      return true;
    }
    
    
    
    
  }
	/*
	 * saveFormTranslation
	 * Determines the fields to translate
	 */
	protected function savePropertyTranslation($lang='en-GB')
	{
		// If the language of the property (as when it was created) is the same as the editing language then we don't need to do anything.
		if ($this->lang == $lang || !$this->lang) return true;
		
		// Get an instance of the JTable for the HelloWorld_translations table
		$existingTranslations = JTable::getInstance('HelloWorld_translations', 'HelloWorldTable');

		// Load a copy of all the existing translations for this property, returns null if none found
		$existingTranslations->load(array('property_id'=>$this->id));
	
		// An array of all the translatable fields
		$value = array();
		$value['title'] = $this->title;
		$value['description'] = $this->description;
		$value['property_id'] = $this->id;
		$value['lang_code'] = $lang;
		
		unset($this->title);
		unset($this->description);
			
		// Bind the translated fields to the JTable instance	
		if (!$existingTranslations->bind($value))
		{
			JError::raiseWarning(500, $existingTranslations->getError());
			return false;
		}	

		// And update or create depending on whether any translations already exist
		if (!$existingTranslations->store())
		{
			JError::raiseWarning(500, $existingTranslations->getError());
			return false;
		}	
	}

	/*
	 * loadFormTranslations
	 * Determines the translated fields to load depentent on the current editing language
	 */
	protected function loadPropertyTranslation($lang='en-GB')
	{
		// If the language of the property (when it was created) is the same as the current editing language 
		// then we don't need to do anything. That is, we just show the fields as they come
		if ($this->lang == $lang || !$this->lang) return true;
		
		// Get an instance of the JTable for the HelloWorld_translations table
		$existingTranslations = JTable::getInstance('HelloWorld_translations', 'HelloWorldTable');

		// Load a copy of all the existing translations for this property, returns null if none found
		$existingTranslations->load(array('property_id'=>$this->id));
    
		// Replace the loaded strings with the translated ones
		$this->title = $existingTranslations->title;
		$this->description = $existingTranslations->description;
	}
  
  
  /**
   * save property tariffs, if there are any tariffs available in the POST data then we process them and save them.
   * 
   *  
   */
  protected function savePropertyTariffs( $POST = array() ) {

    if(!array($POST)) {
      return true;
    }
    
    // Check for a tariffs array in the POST structure
    if (!array_key_exists('tariffs', $POST)) {
      return true;
    }

    $tariffs_by_day = $this->getTariffsByDay($POST['tariffs']); 
    $tariff_periods = $this->getAvailabilityByPeriod($tariffs_by_day);

    // Get instance of the tariffs table
    $tariffsTable = JTable::getInstance($type = 'Tariffs', $prefix = 'HelloWorldTable', $config = array());


    // Bind the translated fields to the JTAble instance	
    if (!$tariffsTable->save($this->id, $tariff_periods)) {
      JApplication::enqueueMessage(JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_UNIT_TO_BE_ADDED'), 'warning');

      JError::raiseWarning(500, $tariffsTable->getError());
      return false;
    }
  }

  /**
   * Generates an array containing a day for each tariff period passed in via the form. Ensure that any new periods are
   * merged into the data before saving.
	 *
	 * Returns an array of tariffs per days based on tariff periods.
   * 
   * @param array $tariffs An array of tariffs periods as passed in via the tariffs admin screen
   * @return array An array of availability, by day. If new start and end dates are passed then these are included in the returned array
   * 
   */
	protected function getTariffsByDay ( $tariffs = array() ) 
	{
    // Array to hold availability per day for each day that availability has been set for.
    // This is needed as availability is stored by period, but displayed by day.
    $raw_tariffs = array();

    // Generate a DateInterval object which is re-used in the below loop
    $DateInterval = new DateInterval('P1D');

    // For each tariff period passed in first need to determine how many tariff periods there are
    $tariff_periods = count($tariffs['start_date']);

    for ($k = 0; $k < $tariff_periods; $k++) {

      $tariff_period_start_date = '';
      $tariff_period_end_date = '';
      $tariff_period_length = '';

      // Check that availability period is set for this loop. Possible that empty array elements exists as additional
      // tariff fields are added to the form in case owner wants to add additional tariffs etc
      try {  
      
      if ($tariffs['start_date'][$k] != '' && $tariffs['end_date'][$k] != '' && $tariffs['tariff'][$k] != '') {

        // Convert the availability period start date to a PHP date object
        $tariff_period_start_date = new DateTime($tariffs['start_date'][$k]);

        // Convert the availability period end date to a date 
        $tariff_period_end_date = new DateTime($tariffs['end_date'][$k]);

        // Calculate the length of the availability period in days
        $tariff_period_length = date_diff($tariff_period_start_date, $tariff_period_end_date);

        // Loop from the start date to the end date adding an available day to the availability array for each availalable day
        for ($i = 0; $i <= $tariff_period_length->days; $i++) {

          // Add the day as an array key storing the availability status as the value
          $raw_tariffs[date_format($tariff_period_start_date, 'Y-m-d')] = $tariffs['tariff'][$k];

          // Add one day to the start date for each day of availability
          $date = $tariff_period_start_date->add($DateInterval);
        }
      }
      
      } catch (Exception $e ) {
        //TO DO - Log this
        print_r($e);die;
      }
      
    }

    return $raw_tariffs;
  } 
  
  /**
   * Given an array of availability by day returns an array of availability periods, ready for insert into the db
   *  
   * @param array $availability_by_day An array of days containing the availability status
   * @return array An array of availability periods
   * 
   */
  public function getAvailabilityByPeriod($availability_by_day = array()) {
    $current_status = '';
    $availability_by_period = array();
    $counter = 0;

    $last_date = key(array_slice($availability_by_day, -1, 1, TRUE));

    foreach ($availability_by_day as $day => $status) {
      if (($status !== $current_status) || ( date_diff(new DateTime($last_date), new DateTime($day))->days > 1 )) {
        $counter++;
        $availability_by_period[$counter]['start_date'] = $day;
        $availability_by_period[$counter]['end_date'] = $day;
        $availability_by_period[$counter]['status'] = $status;
      } else {
        $availability_by_period[$counter]['end_date'] = $day;
      }

      $current_status = $status;
      $last_date = $day;
    }
    return $availability_by_period;
  }

  public function saveImageDetails( $POST = array() ) {
    
    /*
     * Declare some variables
     * 
     */
    $library_images = array();
    $gallery_images = array();

    
    if(!array_key_exists('library-images',$POST)) {
      return true;
    }
    
    // Scope parent_id locally
    $parent_id = $this->parent_id;
    
    // Check for a images array in the POST structure
    if (!array_key_exists('gallery-images', $POST)) {
      $gallery_images = array();
    } else {
      $gallery_images = $POST['gallery-images'];  
    }

    // Get instance of the images table
    $imagesTable = JTable::getInstance($type = 'Images', $prefix = 'HelloWorldTable', $config = array());
    
    // Check for library images. This could be empty in several scenarios
    // 1. The property has no units
    // 2. It is a multi unit property but all images are assign to the gallery
    // 3. No images have been uploaded
    // 4. We are editing the parent property. In this case we don't want to delete all the associated images before inserting into the gallery...
    
    if (array_key_exists('library-images', $POST)) {
      $library_images = $POST['library-images'];      
    }
    // Delete existing images
    // Need to wrap this in some logic
    // Only ever need to delete all images from the gallery library (because library needs to be maintained). Gallery is 
    // generated by showing only those not assigned to the gallery...
    // Get the subtree for this property
    $subtree = $this->getTree( $this->id );	
      
    // This deletes the existing images for a gallery 
    if (count($subtree) == 1 && $this->isLeaf( $this->id ) && $parent_id == 1) { // This must be a single unit property...
      
      // The only time we need to delete and reinsert into the library images table
      $imagesTable->delete_images($this->id);
      
      // Save the images back to the library table (as this must be a single unit property without units).
      // TO DO - Check the above more thorougly, with a function?
      if (count($gallery_images)) {
        if (!$imagesTable->save_images($this->id, $gallery_images, true)) {
          JError::raiseWarning(500, $imagesTable->getError());
          return false;
        }
      }          
      
      // Tick the images progress flag to true
      JApplication::setUserState('com_helloworld.images.progress', true);
      
    } else {
      // Set the $parent_id to the property ID so images are assigned to the gallery.

      $image_gallery_table = JTable::getInstance('Gallery_images', 'HelloWorldTable');
      
      // Delete any existing assigned gallery images
      $image_gallery_table->delete_images($this->id);
     
      // Save the images back to the library table (as this must be a single unit property without units).
      // TO DO - Check the above more thorougly, with a function?
      if (count($gallery_images)) {
        if (!$image_gallery_table->save_images($this->id, $gallery_images, true )) {
          JError::raiseWarning(500, $imagesTable->getError());
          return false;
        }
      }  
      
      // Tick the images progress flag to true
      JApplication::setUserState('com_helloworld.images.progress', true);
      
    }
    
  }
  
  	/**
	 * Method to get a node and all its child nodes.
	 *
	 * @param   integer  $pk          Primary key of the node for which to get the tree.
	 * @param   boolean  $diagnostic  Only select diagnostic data for the nested sets.
	 *
	 * @return  mixed    Boolean false on failure or array of node objects on success.
	 *
	 * @since   11.1
	 * @throws  RuntimeException on database error.
	 */
	public function getUnitTree($pk = null, $diagnostic = false)
	{
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// Get the node and children as a tree.
		$query = $this->_db->getQuery(true);
		$select = ($diagnostic) ? 'n.title,n.occupancy,n.' . $k . ', n.parent_id, n.level, n.lft, n.rgt,(n.single_bedrooms + n.double_bedrooms + n.triple_bedrooms + n.quad_bedrooms + n.twin_bedrooms) as bedrooms' : 'n.*';
		$query->select($select)
			->from($this->_tbl . ' AS n, ' . $this->_tbl . ' AS p')
			->where('n.lft BETWEEN p.lft AND p.rgt')
			->where('p.' . $k . ' = ' . (int) $pk)
			->order('n.lft');

		return $this->_db->setQuery($query)->loadObjectList();
	}
}
