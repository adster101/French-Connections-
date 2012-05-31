<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class JFormFieldUserProperties extends JFormFieldList
{
	/**
	 * Returns a list of properties that are also owned by the created_id of the property being edited.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'UserProperties';
	
	/**
	 * Based on the created_by field of the item being edited we pull out a list of other properties 
	 * owned by this user. Note that we only show those at level 1 to prevent grand children 
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		$published = $this->element['published']? $this->element['published'] : array(0,1);
		$name = (string) $this->element['name'];

		// Let's get the id for the current property
		$jinput = JFactory::getApplication()->input;
		
		// Get the database instance
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		// Get current logged in user
		// If this is super user then too bad if they've created loads of properties....
		$user = JFactory::getUser();
		
		// Select all 
		$query->select('a.id, a.greeting');
		$query->from('#__helloworld AS a');
		$query->where('created_by = '.$user->id);
		$query->where('level = 1');
		
		// Get the options.
		$db->setQuery($query);

		$properties = $db->loadObjectList();
		// Loop over each subtree item
		foreach($properties as $property) 
		{		

			$options[] = JHtml::_('select.option', $property->id, $property->greeting);
		}
		
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}
	