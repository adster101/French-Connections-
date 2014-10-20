<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * User notes model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class NotesModelNotes extends JModelList
{

  /**
   * Class constructor.
   *
   * @param  array  $config  An optional associative array of configuration settings.
   *
   * @since  2.5
   */
  public function __construct($config = array())
  {
    // Set the list ordering fields.
    if (empty($config['filter_fields']))
    {
      $config['filter_fields'] = array(
          'a.created_on',
          'limitstart',
          'listlimit',
          'state'
      );
    }

    parent::__construct($config);
  }

  /**
   * Build an SQL query to load the list data.
   *
   * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
   *
   * @since   2.5
   */
  protected function getListQuery()
  {
    $input = JFactory::getApplication()->input;
    $layout = $input->get('layout', '', 'string');
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    $query->from('#__listing_notes AS a');

    // Filter by a single user.
    $search = (int) $this->getState('filter.search');

    // If we're in a modal layout assume we have a property ID that we want to filter on.
    if ($layout == 'modal')
    {
      $search = $input->get('property_id', '', 'int');
    }

    // Select the required fields from the table.
    $query->select('
        a.id,
        a.subject,
        a.created_on,
        a.property_id,
        a.body
      ');

    if (!empty($search))
    {
      $query->where('a.property_id = ' . (int) $search);
    }

    // Add the list ordering clause.
    $orderCol = $this->state->get('list.ordering');
    $orderDirn = $this->state->get('list.direction');

    $query->order($db->escape($orderCol . ' ' . $orderDirn));

    return $query;
  }

  /**
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @return  void
   *
   * @since   1.6
   */
  protected function populateState($ordering = null, $direction = null)
  {

    $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
    $this->setState('filter.search', $search);

    // List state information.
    parent::populateState('a.created_on', 'desc');
  }

}
