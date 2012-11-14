<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.tablenested');

class HelloWorldTableHelloWorld_userproperties extends JTableNested 
{
	/*
	 * Access the HelloWorld_translations table, baby
	 * 
	 */
	var $id = '';
	var $parent_id = 0;
	var $lft = 0;
	var $rgt = 0;
	var $level = '';
	var $title = '';	
	var $alias = '';	
	var $access = '';	
	var $path = '';
	var $catid = '';
	var $params = '';
	var $created_by = 0;
	var $modified = '';	
	var $modified_by = '';
	var $lang = '';
	var $description = '';
	var $occupancy = '';
	var $swimming = '';
	var $latitude = '';
	var $longitude = '';	
	var $nearest_town = '';
	var $distance_to_coast = '';
	
	function __construct(&$db)
	{
		parent::__construct('#__helloworld', 'id', $db);
	}
}

