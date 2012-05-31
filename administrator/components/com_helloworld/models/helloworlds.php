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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param	string	An optional ordering field.
	 * @param	string	An optional direction (asc|desc).
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.lft', 'asc');
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.language');

		return parent::getStoreId($id);
	}
	

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
		
		$listOrdering = $this->getState('list.ordering', 'a.lft');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));

		$query->order($db->escape($listOrdering).' '.$listDirn);

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
