<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.tablenested');

class RentalTableHelloWorld_userproperties extends JTableNested 
{
	
	function __construct(&$db)
	{
		parent::__construct('#__helloworld', 'id', $db);
	}
}

