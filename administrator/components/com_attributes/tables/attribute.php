<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.table');

// import the model helper lib
jimport('joomla.application.component.model');

/**
 * Hello Table class
 */
class AttributeTableAttribute extends JTable
{
  
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__attributes', 'id', $db);
	}
  
  public function store (){
    $lang = JApplication::getUserState('com_attributes.edit.lang','en-GB');
    
    // TO DO: Determine if this is a 'translation' - for now, determine this is the case if the editing language is fr-FR
		$this->saveAttributeTranslation($lang);
    
    // Attempt to store the data.
		return parent::store($updateNulls);
  }
    

  public function load($pk = null, $reset = true) 
	{		
		if (parent::load($pk, $reset)) 
		{    
      // Set the title here so we can see what attribute we are editing
      JToolBarHelper::title(JText::_('Manage attribute ('. $this->title.')'));
			// Get the current editing language for this property    
      $lang = JApplication::getUserState('com_attributes.edit.lang','en-GB');

      // Need to load any translations here if the editing language different from the property language
			$this->loadAttributeTranslation($lang);
      
			return true;
		}
		else
		{
			return false;
		}
	}
  
	/*
	 * loadAttributeTranslations
	 * Determines the translated fields to load depentent on the current editing language
   * 
	 */
	protected function loadAttributeTranslation($lang='en-GB')
	{
  
    // If the language of the attribute (when it was created) is the same as the current editing language 
		// then we don't need to do anything. That is, we just show the fields as they come
		if ($this->language_code == $lang || !$this->language_code) return true;
		// Get an instance of the JTable for the HelloWorld_translations table
		$existingTranslations = JTable::getInstance('AttributeTranslation', 'AttributesTable');

		// Load a copy of all the existing translations for this property, returns null if none found
		$existingTranslations->load(array('id'=>$this->id));
    // Replace the loaded strings with the translated ones
		$this->title = $existingTranslations->title;

  }  
  
	/*
	 * saveFormTranslation
	 * Determines the fields to translate
	 */
	protected function saveAttributeTranslation($lang='en-GB')
	{
		// If the language of the property (as when it was created) is the same as the editing language then we don't need to do anything.
		if ($this->language_code == $lang || !$this->language_code) return true;
		
		// Get an instance of the JTable for the HelloWorld_translations table
		$existingTranslations = JTable::getInstance('AttributeTranslation', 'AttributesTable');

		// Load a copy of all the existing translations for this property, returns null if none found
		$existingTranslations->load(array('id'=>$this->id));
	
		// An array of all the translatable fields
		$value = array();
		$value['title'] = $this->title;
		$value['attribute_id'] = $this->id;
		$value['language_code'] = $lang;
		
		unset($this->title);
			
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
}
