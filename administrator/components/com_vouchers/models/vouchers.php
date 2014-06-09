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
class VouchersModelVouchers extends JModelList {

  /**
   * Constructor.
   *
   * @param    array    An optional associative array of configuration settings.
   * @see        JController
   * @since    1.6
   */
  public function __construct($config = array()) {
    if (empty($config['filter_fields'])) {
      $config['filter_fields'] = array(
          'id', 'a.id',
          'created_by', 'a.created_by',
          'user_id', 'a.user_id',
          'date_created', 'a.date_created',
          'state', 'a.state',
          'property_id', 'a.property_id',
          'due_date', 'a.due_date',
          'item_cost_id', 'a.item_cost_id'
      );
    }

    parent::__construct($config);
  }

  /**
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   */
  protected function populateState($ordering = null, $direction = null) {

    // List state information.
    parent::populateState('a.id', 'asc');
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
  protected function getStoreId($id = '') {
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
  protected function getListQuery() {
    // Create a new query object.
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    $user = JFactory::getUser();

    // Get invoice editing permissions
    $canDo = InvoicesHelper::getActions();

    // Select the required fields from the table.
    $query->select(
            $this->getState(
                      'list.select', 
                      'a.id,a.date_created, date_format(a.end_date, "%d %M %Y") as end_date,a.quantity, a.item_cost_id, a.state, a.property_id, b.description'
            )
    );
    $query->from('`#__vouchers` AS a');

    $query->leftJoin('#__item_costs b on a.item_cost_id = b.code');

    // Filter by published state
    $published = $this->getState('filter.state');
    if (is_numeric($published)) {
      $query->where('a.state = ' . (int) $published);
    } else if ($published === '') {
      $query->where('(a.state IN (0, 1))');
    }

    // Filter by search on property number
    $search = $this->getState('filter.search');
    if (!empty($search)) {

      if (stripos($search, 'itc:') === 0) {
        $search = $db->Quote('%' . $db->escape(substr($search, 4), true) . '%');
        $query->where('(a.item_cost_id like ' . $search . ' OR b.description LIKE ' . $search . ')');
      } else {

        $query->where('a.property_id = ' . (int) $search);
      }
    }

    //Filtering due_date
    $filter_due_date_from = $this->state->get("filter.date_created");
    if ($filter_due_date_from) {
      $query->where("a.date_created >= '" . $db->escape($filter_due_date_from) . "'");
    }
    $filter_due_date_to = $this->state->get("filter.end_date");
    if ($filter_due_date_to) {
      $query->where("a.end_date <= '" . $db->escape($filter_due_date_to) . "'");
    }


    // Add the list ordering clause.
    $orderCol = $this->state->get('list.ordering');
    $orderDirn = $this->state->get('list.direction');
    if ($orderCol && $orderDirn) {
      $query->order($db->escape($orderCol . ' ' . $orderDirn));
    }



    return $query;
  }

}
