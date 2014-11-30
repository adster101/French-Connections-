<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */

class AutoRenewalsModelAutoRenewals extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
   * 
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

  /**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
   *
	 */
	protected function getListQuery()
	{

    // Get the user to authorise
    $user	= JFactory::getUser();

		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('
      a.id, 
      a.expiry_date,
      b.CardType,
      b.CardLastFourDigits,
      b.CardExpiryDate,
      a.VendorTxCode
    ');

		// From the hello table
		$query->from('#__property a');
    
    // Join the category 
    $query->join('left', '#__protx_transactions b on b.id = a.VendorTxCode');

    //
    $query->where('a.expiry_date >= ' . $db->quote(JFactory::getDate()->calendar('Y-m-d')));
    $query->where('a.VendorTxCode !=\'\'');
    	
    $listOrdering = $this->getState('list.ordering','expiry_date');
		$listDirn = $db->escape($this->getState('list.direction', 'asc'));
    $query->order($db->escape($listOrdering).' '.$listDirn);

    return $query;
	}
}

