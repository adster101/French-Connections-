<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.tablenested');

class HelloWorldTableHelloWorld_categories extends JTableNested 
{
	/*
	 * Woot! Access the HelloWorld_translations table, baby
	 * Since, I wrote it.
	 */
	var $id = '';
	var $asset_id = '';
	var $parent_id = '';
	var $lft = '';
	var $rgt = '';
	var $level = '';
	var $path = '';
	var $extension = '';
	var $title = '';	
	var $alias = '';	
	var $note = '';
	var $description = '';
	var $published = '';
	var $checked_out = '';
	var $checked_out_time = '';
	var $access = '';	
	var $params= '';
	var $metadesc = '';
	var $metakey = '';
	var $metadata = '';
	var $created_user_id = '';
	var $created_time = '';
	var $modified_user_ud = '';	
	var $modified_time = '';
	var $hits = '';
	var $language = '';
	
	function __construct(&$db)
	{
		parent::__construct('#__categories', 'id', $db);
	}

}

