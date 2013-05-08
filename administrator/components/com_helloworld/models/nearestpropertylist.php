<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * HelloWorldList Model
 */
class HelloWorldModelNearestPropertyList extends JModelList
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
			$props[$k]['title'] = $v->title . round($v->distance,2) . ' Miles';
		}

    $choose = array('id'=>'','title'=>'Please choose');

    // Add the placeholder
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

    $latitude = $input->get('lat','','string');
    $longitude = $input->get('lon','','string');

    $query->select('id, title, level');
    $query->select(
      '(
        3959 * acos( cos( radians(' . $longitude . ') )
        * cos( radians( latitude ) )
        * cos( radians( longitude ) -
        radians('.$latitude.') ) +
        sin( radians(' . $longitude . ') )
        * sin( radians( latitude ) ) ) )
        AS distance
            ');
    $query->from('#__classifications');
    $query->where('level = 5');

    $query->having('distance < 50');
    $query->order('distance');

		return $query;
	}


}
