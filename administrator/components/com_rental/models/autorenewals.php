<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class RentalModelAutoRenewals extends JModelList
{

  public $extension = 'com_rental';

  public function __construct($config = array())
  {
    if (empty($config['filter_fields']))
    {
      $config['filter_fields'] = array(
          'a.id', 'a.id'
      );
    }
    parent::__construct($config);

  }



  /**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
	    // List state information.
		parent::populateState('a.id','desc');
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

    $app = JFactory::getApplication();
    $id = $app->input->get('id', 'int');

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('CardLastFourDigits, a.id, a.VendorTxCode, CardType, CardExpiryDate, b.VendorTxCode as current');
    $query->from('#__protx_transactions a');
    $query->where('property_id = ' . (int) $id);
    $query->join('left', '#__property b on a.id = b.VendorTxCode');
    //$query->group('CardLastFourDigits');
    $query->where($db->quoteName('Status') . ' = ' . $db->quote('OK'));
    $listOrdering = $this->getState('list.ordering', 'a.id');
    $listDirn = $db->escape($this->getState('list.direction', 'desc'));

    // Order if we have a specific ordering.
    if ($listOrdering)
    {
      $query->order($db->escape($listOrdering) . ' ' . $listDirn);
    }
    return $query;

  }
}
