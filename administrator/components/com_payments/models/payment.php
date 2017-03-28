<?php
/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Invoices records.
 */
class PaymentsModelPayment extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(

            );
        }

        parent::__construct($config);
    }


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

    $id = $app->getUserStateFromRequest($this->context.'.payment.id','id','','int');
    $this->setState($this->context.'.payment.id',$id);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);



		// Load the parameters.
		$params = JComponentHelper::getParams('com_payments');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.property_id', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

    $user = JFactory::getUser();

    $query->select('*, (ptl.quantity * ptl.cost) as line_total, u.name');
    $query->from('#__protx_transactions pt');

    $query->leftJoin('#__protx_transaction_lines ptl on ptl.VendorTxCode = pt.VendorTxCode');
    $query->leftJoin('#__property p on p.id = pt.property_id');
    $query->leftJoin('#__item_costs ic on ic.code = ptl.code');
    $query->leftJoin('#__users u on u.id = pt.user_id');
    $query->where('pt.id = ' . (int) $this->getState($this->context.'.payment.id',''));

		$canDo	= PaymentsHelper::getActions();

    if (!$canDo->get('core.edit') && $canDo->get('code.edit.own')) {
      $query->where('i.user_id = ' . (int) $user->id);
    }

		return $query;
	}
}