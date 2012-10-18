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
class JFormFieldClassificationTypes extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'ClassificationTypes';

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
    
    
    $db		= JFactory::getDbo();
    
		$query	= $db->getQuery(true);
		$query->select('a.id as value, a.title AS text, a.level, a.published, a.language_string');
		$query->from('#__classifications AS a');
		$query->join('LEFT', $db->quoteName('#__classifications').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		$query->where('b.id='.$classificationID);
    $query->where('a.published = 1');
		$query->group('a.id, a.title, a.level, a.lft, a.rgt, a.parent_id, a.published');
		$query->order('a.lft ASC');
		
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
