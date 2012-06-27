<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.tablenested');
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
		if (isset($array['params']) && is_array($array['params']) && $this->params) 
		{
			// $this is an instance of HelloWorldTableHelloWorld (and includes a copy of the record as it stands in the db)
			// Loop over the $array['params']
			// For each check that this key isn't already stored as attribute
			// If it is delete it as there is a new one incoming
			// merge the two sets of data
			// so that both sets of params are preserved
			foreach($array['params'] as $key=>$value) {
				if ($this->params->getValue($key) !== null) {
					$tmp = $this->params->set($key, '');
				}
			}			
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
	public function load($pk = null, $reset = true) 
	{		
		if (parent::load($pk, $reset)) 
		{
			// Get the current editing language for this property
			$lang = HelloWorldHelper::getLang();	
			// Need to load any translations here if the editing language different from the property language
			$this->loadPropertyTranslation($lang);


			// Convert the params field to a registry.
			$params = new JRegistry;
			$params->loadJSON($this->params);
			$this->params = $params;
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
		return $this->greeting;
	}
 
	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 * @since	1.6
	 */
	protected function _getAssetParentId()
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_helloworld');
		return $asset->id;
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

		$POST = JRequest::getVar('jform');
		
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
		
		// Do we have availability data to update?
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

        // Bind the translated fields to the JTAble instance	
        if (!$availabilityTable->save($this->id, $availability_by_period))
        {
          JError::raiseWarning(500, $availabilityTable->getError());
          return false;
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
		}
		
		// Verify that the alias is unique
		//$table = JTable::getInstance('HelloWorld', 'HelloWorldTable');

		//if ($table->load(array('alias'=>$this->alias, 'catid'=>$this->catid)) && ($table->id != $this->id || $this->id==0)) {
			//$this->setError(JText::_('COM_CONTACT_ERROR_UNIQUE_ALIAS'));
			//return false;
		//}
		
		$this->setLocation($this->parent_id, 'last-child');
		

		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	/*
	 * saveFormTranslation
	 * Determines the fields to translate
	 */
	function savePropertyTranslation($lang='en-GB')
	{
		// If the language of the property (as when it was created) is the same as the editing language then we don't need to do anything.
		if ($this->lang == $lang || !$this->lang) return true;
		
		// Get an instance of the JTable for the HelloWorld_translations table
		$existingTranslations = JTable::getInstance('HelloWorld_translations', 'HelloWorldTable');

		// Load a copy of all the existing translations for this property, returns null if none found
		$existingTranslations->load(array('property_id'=>$this->id));
	
		// An array of all the translatable fields
		$value = array();
		$value['greeting'] = $this->greeting;
		$value['description'] = $this->description;
		$value['property_id'] = $this->id;
		$value['lang_code'] = $lang;
		
		unset($this->greeting);
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
	function loadPropertyTranslation($lang='en-GB')
	{
		// If the language of the property (when it was created) is the same as the current editing language 
		// then we don't need to do anything. That is, we just show the fields as they come
		if ($this->lang == $lang || !$this->lang) return true;
		
		// Get an instance of the JTable for the HelloWorld_translations table
		$existingTranslations = JTable::getInstance('HelloWorld_translations', 'HelloWorldTable');

		// Load a copy of all the existing translations for this property, returns null if none found
		$existingTranslations->load(array('property_id'=>$this->id));
    
		// Replace the loaded strings with the translated ones
		$this->greeting = $existingTranslations->greeting;
		$this->description = $existingTranslations->description;
	}
}
