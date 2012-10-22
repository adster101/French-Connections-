<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @since		1.6
 */
class JFormFieldAttributeType extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'attributetype';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
    $options = array();
    
    // This is passed in from the form field XML definition
 		$classificationID = $this->element['id']? $this->element['id'] : 1;
		
    $showPlaceHolder = $this->element['placeholder'] ? $this->element['placeholder'] : 0; 
    
    $lang = JFactory::getLanguage();
    $db		= JFactory::getDbo();

    $query	= $db->getQuery(true);
    
    // Retrieve based on the current editing language
    if ($lang->getTag() === 'en-GB') {
      $query->select('a.id as value, a.title AS text,  a.published');
    } else {
      $query->select('a.id as value, c.title as text, a.published');
    }
		$query->from('#__attributes AS a');
    $query->join('LEFT', $db->quoteName('#__attributes_type').' AS b ON a.attribute_type_id = b.id');

    // If any other language that en-GB load in the translation based on the lang->getTag() function...
    if ($lang->getTag() != 'en-GB') {  
      $query->join('LEFT', $db->quoteName('#__attributes_translation').' as c on c.attribute_id = a.id');
      $query->where('c.language_code = ' . $lang->getTag());
    }
    
		$query->where('b.id='.$classificationID);
    $query->where('a.published = 1');
		
    
    
    // Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

    // Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
    
    // Show a 'please choose' placeholder for single select drop downs
    if ($showPlaceHolder == 'true') {
      // Add an initial 'please choose' option
    	array_unshift($options, JHtml::_('select.option', '', JText::_('COM_HELLOWORLD_PLEASE_CHOOSE')));
    }
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
        
		return $options;
	}
}
