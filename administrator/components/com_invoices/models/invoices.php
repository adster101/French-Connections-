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
class InvoicesModelinvoices extends JModelList
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
    if (empty($config['filter_fields']))
    {
      $config['filter_fields'] = array(
          'id', 'a.id',
          'created_by', 'a.created_by',
          'user_id', 'a.user_id',
          'date_created', 'a.date_created',
          'currency', 'a.currency',
          'exchange_rate', 'a.exchange_rate',
          'invoice_type', 'a.invoice_type',
          'journal_memo', 'a.journal_memo',
          'total_net', 'a.total_net',
          'vat', 'a.vat',
          'state', 'a.state',
          'property_id', 'a.property_id',
          'due_date', 'a.due_date',
          'salutation', 'a.salutation',
          'first_name', 'a.first_name',
          'surname', 'a.surname',
          'address', 'a.address',
          'town', 'a.town',
          'county', 'a.county',
          'postcode', 'a.postcode',
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
    $app = JFactory::getApplication('administrator');

    // Load the filter state.
    $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
    $this->setState('filter.search', $search);

    $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
    $this->setState('filter.state', $published);


    //Filtering due_date
    $this->setState('filter.due_date.from', $app->getUserStateFromRequest($this->context . '.filter.due_date.from', 'filter_from_due_date', '', 'string'));
    $this->setState('filter.due_date.to', $app->getUserStateFromRequest($this->context . '.filter.due_date.to', 'filter_to_due_date', '', 'string'));


    // Load the parameters.
    $params = JComponentHelper::getParams('com_invoices');
    $this->setState('params', $params);

    // List state information.
    parent::populateState('a.date_created', 'desc');
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
    // Create a new query object.
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    $user = JFactory::getUser();

    // Get invoice editing permissions
    $canDo = InvoicesHelper::getActions();

    // Select the required fields from the table.
    $query->select(
            $this->getState(
                    'list.select', 'a.*'
            )
    );
    $query->from('`#__invoices` AS a');


    // Filter on user
    if (!$canDo->get('core.edit') && $canDo->get('core.edit.own'))
    {
      $query->where('a.user_id = ' . (int) $user->id);
    }

    // Filter by published state
    $published = $this->getState('filter.state');
    if (is_numeric($published))
    {
      $query->where('a.state = ' . (int) $published);
    }
    else if ($published === '')
    {
      $query->where('(a.state IN (0, 1))');
    }

    // Filter by search in title
    $search = $this->getState('filter.search');
    if (!empty($search))
    {
      if ((int) $search)
      {
        $query->where('a.property_id = ' . (int) $search);
      }
      else
      {
        $search = $db->Quote('%' . $db->escape($search, true) . '%');
        $query->where('( a.property_id LIKE ' . $search . '  OR  a.first_name LIKE ' . $search . '  OR  a.surname LIKE ' . $search . '  OR  a.town LIKE ' . $search . '  OR  a.postcode LIKE ' . $search . ' )');
      }
    }

    //Filtering due_date
    $filter_due_date_from = $this->state->get("filter.due_date.from");
    if ($filter_due_date_from)
    {
      $query->where("a.due_date >= '" . $db->escape($filter_due_date_from) . "'");
    }
    $filter_due_date_to = $this->state->get("filter.due_date.to");
    if ($filter_due_date_to)
    {
      $query->where("a.due_date <= '" . $db->escape($filter_due_date_to) . "'");
    }


    // Add the list ordering clause.
    $orderCol = $this->state->get('list.ordering');
    $orderDirn = $this->state->get('list.direction');
    if ($orderCol && $orderDirn)
    {
      $query->order($db->escape($orderCol . ' ' . $orderDirn));
    }



    return $query;
  }

}
