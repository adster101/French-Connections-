<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * HelloWorldList Model
 */
class HelloWorldModelPropertyList extends JModelList
{
  
  	/**
	 * Method to get an array of data items.
	 *
	 * @return  array  An array of data items.
	 *
	 * @since   2.5
	 */
	public function getItems()
	{
		// Get the items.
		$items = parent::getItems();
    
    $props = array();

		// Convert them to a simple array.
		foreach ($items as $k => $v)
		{
      $props[$k]['id'] = $v->id;
			$props[$k]['title'] = $v->title;
		}
    
    $choose = array('id'=>'','title'=>'Please choose');
    $parent = array('id'=>'1','title'=>'None. (I.e. not a unit');

    // Add the placeholder
    array_unshift($props, $parent);
    array_unshift($props, $choose);

    return $props;
	}

	/**
	 * Method to build a database query to load the list data.
	 *
	 * @return  JDatabaseQuery  A database query
	 *
	 * @since   2.5
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

    $input = JFactory::getApplication()->input;
    
    $id = $input->get('id');
    
    $query->select('a.id, a.title'); 
    $query->from('#__helloworld AS a');
    $query->where('created_by = '.$id);		// Select only the props created by the user that created this property
    $query->where('level = 1');	// Only show those that are at level 1 

		return $query;
	}
  
	
}
