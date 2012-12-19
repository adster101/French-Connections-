<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * HelloWorldList Model
 */
class HelloWorldModelReviews extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	public function getListQuery()
	{
		// Get the user ID
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

    // Get the property ID
    $property_id = JRequest::getVar('id','','GET','int'); 
    
    // Get the list of user groups this user is assigned to		
		$groups = $user->getAuthorisedGroups();
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select some fields
		$query->select('*');
    
		// From the special offers table
		$query->from('#__reviews as a');
    
    // Only want those assigned to the current property
    $query->where('property_id = ' . $property_id);
		
		return $query;
	}


}
