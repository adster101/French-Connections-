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
class JFormFieldClassificationParent extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'ClassificationParent';

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

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

    $query->select('a.id, a.title, a.level');
    $query->from('#__classifications AS a');
    $query->where('a.parent_id > 0');
		$query->where('a.published != -2');
    
		// Filter out the record being edited from appearing in the list
		if ($id = $this->form->getValue('id')) {    
      $query->where('a.id != ' . $id);
      $query->where('a.level < 4');
    }
		$query->group('a.id, a.title, a.level, a.lft, a.rgt, a.parent_id, a.published');
		$query->order('a.lft ASC');


    $db->setQuery($query);
    $items = $db->loadObjectList();

		$options = array();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
			// Loop over each subtree item
			foreach($items as &$item) 
			{
				$repeat = ($item->level - 1 >= 0) ? $item->level - 1 : 0;
				$item->title = str_repeat('- ', $repeat) . $item->title;
				$options[] = JHtml::_('select.option', $item->id, $item->title);
			}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
