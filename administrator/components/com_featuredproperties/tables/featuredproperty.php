<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla nested table library
jimport('joomla.database.table');

// import the model helper lib
jimport('joomla.application.component.model');

/**
 * Hello Table class
 */
class FeaturedPropertiesTableFeaturedProperty extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__featured_properties', 'id', $db);
	}

}
