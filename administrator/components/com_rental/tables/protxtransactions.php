<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla nested table library
jimport('joomla.database.table');

// import the model helper lib
//jimport('joomla.application.component.model');

/**
 * Hello Table class
 */
class RentalTableProtxTransactions extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__protx_transactions', 'id', $db);
	}
}
