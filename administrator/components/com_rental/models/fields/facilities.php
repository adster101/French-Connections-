<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
JFormHelper::loadFieldClass('checkboxes');

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Displays options as a list of check boxes.
 * Multiselect may be forced to be true.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @see         JFormFieldCheckbox
 * @since       11.1
 */
class JFormFieldFacilities extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Facilities';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected $forceMultiple = true;


	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();
    
    // This is passed in from the form field XML definition
 		$classificationID = $this->element['id']? $this->element['id'] : 1;  
    
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
      $query->where("c.language_code = '" . $lang->getTag()."'");
    }
    
		$query->where('b.id='.$classificationID);
    $query->where('a.published = 1');
    
    $query->order('text');

    // Get the options.
		$db->setQuery($query);

		$facilities = $db->loadObjectList();

    // Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
      
		foreach ($facilities as $option)
		{
			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', $option->value, $option->text, 'value', 'text',
				( $option->published == 'true')
			);
      
      
			$tmp->checked = false;

			// Add the option object to the result set.
			$options[] = $tmp;
		}


		return $options;
	}
}
