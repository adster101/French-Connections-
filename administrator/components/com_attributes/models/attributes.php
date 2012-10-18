<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */

class AttributesModelAttributes extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select some fields
		$query->select('a.id,a.attribute_type_id,a.title,a.state,at.title as attribute_type');
      
    $query->join('left','#__attributes_type at ON a.attribute_type_id = at.id');
		
		// From the hello table
		$query->from('#__attributes a');
    
		$listOrdering = $this->getState('list.ordering','id');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));

		$query->order($db->escape($listOrdering).' '.$listDirn);

		return $query;
	}   
}