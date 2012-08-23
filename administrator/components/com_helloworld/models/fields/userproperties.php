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
	 * UserProperties fields - a list of properties
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'UserProperties';
	
	/**
	 * Based on the created_by field of the property being edited we pull out a list of other properties 
	 * owned/created by this user. 
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		$created_by = '';
		$userId = '';

		// Initialise variables.
		$options 	= array();

		$id  	 		= JRequest::getInt('id');		// Get the id for the current property

		$db				= JFactory::getDbo();		// Get the database instance

		$query		= $db->getQuery(true); 

		$user 		= JFactory::getUser();	// Get current logged in user

		$groups = $user->getAuthorisedGroups();	// Get the list of user groups this user is assigned to		

		// Need to get the userID for the user who created this property
		// Use the HelloWorld table instance to do this
		// Only needs to run if this is a property edit, otherwise created_by will null anyway
		$table = JTable::getInstance('HelloWorld', 'HelloWorldTable');			
		$table->load(array('id'=>$id));	

		// Get the ID of the user who created the property 	
		if ($id == 0) {
			// If no ID then must be a new property which means no created_by id exists so need to check against property owner
			$created_by = $user->id;
		} else {
			$created_by = $table->created_by;	
		}
		// Logic behind the following is as follows
		// We only want properties that belong to the user who created the property currently being editied, where that user is a property owner or an admin
		// We only want properties at level 1
		// We don't want the property currently being edited
	
		if (in_array(10, $groups) || in_array(8,$groups)) { // This user is in the property owner user group (10) or a super user.
			$query->select('a.id, a.greeting'); 
			$query->from('#__helloworld AS a');
			$query->where('created_by = '.$created_by);		// Select only the props created by the user that created this property
			$query->where('level = 1');	// Only show those that are at level 1 
			if ($id !='') {
				$query->where('id <>'.$id);	// Need to ignore the current property ID (as it cannot parent itself)
			}	
			// Get the options.
			$db->setQuery($query);

			$properties = $db->loadObjectList();
			// Loop over each subtree item
			foreach($properties as $property) 
			{		
				$options[] = JHtml::_('select.option', $property->id, $property->greeting);
			}
		} 
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}
	
