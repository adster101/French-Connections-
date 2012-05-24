<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * Hello Table class
 */
class HelloWorldTableHelloWorld extends JTable
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
			
		// Bind the translated fields to the JTAble instance	
		if (!$existingTranslations->bind($value))
		{
			JError::raiseWarning(500, $form->getError());
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
