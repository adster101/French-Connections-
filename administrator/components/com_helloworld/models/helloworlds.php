<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * HelloWorldList Model
 */
class HelloWorldModelHelloWorlds extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Get the user ID
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		// Get the list of user groups this user is assigned to		
		$groups = $user->getAuthorisedGroups();
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('a.id,a.greeting, a.title, a.modified, a.alias, a.access, a.created_by, a.path, a.parent_id, a.level, a.lft, a.rgt, a.lang,ua.name AS author_name ');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		if (!in_array(8, $groups)) 
		{
		$query->where('created_by='.$userId);
		}
		$query->where('created_by !=0');
		// From the hello table
		$query->from('#__helloworld as a');
		return $query;
	}

	function getLanguages()
	{
		$lang 	   =& JFactory::getLanguage();
		$languages = $lang->getKnownLanguages(JPATH_SITE);
		
		$return = array();
		foreach ($languages as $tag => $properties)
			$return[] = JHTML::_('select.option', $tag, $properties['name']);
		
		return $return;
	}

}
