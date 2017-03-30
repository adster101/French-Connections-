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
class PaymentsModelPayments extends JModelList {

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
          'user_id', 'a.user_id',
          'date_created', 'a.DateCreated',
          'property_id', 'a.property_id',
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
    // Initialise variables.
    $app = JFactory::getApplication('administrator');

    // Load the filter state.
    $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
    $this->setState('filter.search', $search);

    $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
    $this->setState('filter.state', $published);

    // Load the parameters.
    $params = JComponentHelper::getParams('com_invoices');
    $this->setState('params', $params);

    // List state information.
    parent::populateState('a.DateCreated', 'desc');
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
    $canDo = PaymentsHelper::getActions();

    // Select the required fields from the table.
    $query->select(
            $this->getState(
                    'list.select', 'a.*, up.name'
            )
    );
    $query->from('`#__protx_transactions` AS a');

    // Filter by search in title
    $search = $this->getState('filter.search');
    if (!empty($search)) {
      if ((int) $search) {
        $query->where('a.property_id = ' . (int) $search);
      } else {
        $search = $db->Quote('%' . $db->escape($search, true) . '%');
        $query->where('( a.property_id LIKE ' . $search . '  OR  a.first_name LIKE ' . $search . '  OR  a.surname LIKE ' . $search . '  OR  a.town LIKE ' . $search . '  OR  a.postcode LIKE ' . $search . ' )');
      }
    }

    $query->leftJoin('#__users up on up.id = a.user_id');

    $query->where('TxType != \'REPEATDEFERRED\'');

    // Add the list ordering clause.
    $orderCol = $this->state->get('list.ordering');
    $orderDirn = $this->state->get('list.direction');
    if ($orderCol && $orderDirn) {
      $query->order($db->escape($orderCol . ' ' . $orderDirn));
    }



    return $query;
  }

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Payments', $prefix = 'Payments', $config = array()) {

    return JTable::getInstance($type, $prefix, $config);
  }

}
