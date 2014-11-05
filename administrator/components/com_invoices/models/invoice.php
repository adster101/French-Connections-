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
class InvoicesModelinvoice extends JModelList
{

  public function getTable($type = 'Invoice', $prefix = 'InvoicesTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

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
      $config['filter_fields'] = array();
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

    $id = $app->getUserStateFromRequest($this->context . '.invoice.id', 'id', '', 'int');
    $this->setState($this->context . '.invoice.id', $id);

    // Load the filter state.
    $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
    $this->setState('filter.search', $search);

    $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
    $this->setState('filter.state', $published);



    // Load the parameters.
    $params = JComponentHelper::getParams('com_invoices');
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
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    $user = JFactory::getUser();

    $query->select('il.item_code,il.item_description,il.quantity,il.total_net as line_value,il.vat as line_vat_value');
    $query->from('#__invoice_lines il');

    $query->select('i.id,i.due_date,i.property_id,i.date_created,i.total_net,i.vat,i.property_id,i.first_name,i.surname,i.address1,i.address2,i.address3,i.town,i.county,i.postcode');
    $query->leftJoin('#__invoices i on il.invoice_id = i.id');

    $query->where('invoice_id = ' . (int) $this->getState($this->context . '.invoice.id', ''));

    $canDo = InvoicesHelper::getActions();

    if (!$canDo->get('core.edit') && $canDo->get('code.edit.own'))
    {
      $query->where('i.user_id = ' . (int) $user->id);
    }

    return $query;
  }

}