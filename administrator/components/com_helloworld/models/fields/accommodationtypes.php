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
class JFormFieldAccommodationTypes extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Accommodationtypes';

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

    // Get a nested sets table instance for the categories table.
    
    JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_classification/tables');
   
		$table = JTable::getInstance('Classification', 'ClassificationTable');
		
		// To do: Add method to table class to retrieve the node via alias. May be more robust in the long run
		$subTree = $table->getTree(66);

   	// Check for a database error.
		if ($table->getError()) {
			JError::raiseWarning(500, $table->getErrorMsg());
      return false;
		}
		
    // Add an initial 'please choose' option
    $options[] = JHtml::_('select.option', '', JText::_( 'COM_HELLOWORLD_PLEASE_CHOOSE' ));

    if ($subTree)
		{
			// Loop over each subtree item
			foreach($subTree as $item) 
			{
				if($table->isLeaf( $item->id )) {
					$options[] = JHtml::_('select.option', $item->id, JText::_( $item->language_string ));
				}
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
