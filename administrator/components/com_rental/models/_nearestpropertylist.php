<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * HelloWorldList Model
 */
class RentalModelNearestPropertyList extends JModelList
{

  var $latitude = '';
  
  var $longitude = '';
  
  public function __construct($config = array()) {
    
    parent::__construct($config);
    
    
  
    $this->latitude = ($config['latitude']) ? $config['latitude'] : '';
    $this->longitude = ($config['longitude']) ? $config['longitude'] : '';
    
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

    $latitude = $this->latitude;
    $longitude = $this->longitude;

    $query->select('id, title, level');
    $query->select(
      '(
        3959 * acos( cos( radians(' . $latitude . ') )
        * cos( radians( latitude ) )
        * cos( radians( longitude ) -
        radians(' . $longitude . ') ) +
        sin( radians(' . $latitude . ') )
        * sin( radians( latitude ) ) ) )
        AS distance
            ');
    $query->from('#__classifications');
    $query->where('level = 5');

    //$query->having('');
    $query->order('distance');

		return $query;
	}


}
