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
class NotesModelNotes extends JModelList {

  /**
   * Class constructor.
   *
   * @param  array  $config  An optional associative array of configuration settings.
   *
   * @since  2.5
   */
  public function __construct($config = array()) {
    // Set the list ordering fields.
    if (empty($config['filter_fields'])) {
      $config['filter_fields'] = array(
          'id',
          'a.id',
          'a.property_id',
          'u.name',
          'subject',
          'a.subject',
          'catid',
          'a.catid',
          'state', 'a.state',
          'c.title',
          'review_time',
          'a.review_time',
          'publish_up', 'a.publish_up',
          'publish_down', 'a.publish_down',
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
  protected function getListQuery() {
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    $query->from('#__listing_notes AS a');

    // Filter by a single user.
    $property_id = (int) $this->getState('filter.property_id');

    // Select the required fields from the table.
    $query->select('
        a.id,
        a.subject,
        a.created_time,
        a.property_id,
        a.body,
        a.state
        ');

    $query->where('a.property_id = ' . $property_id);

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
  protected function populateState($ordering = null, $direction = null) {
    
		// List state information.
		parent::populateState('a.id', 'asc');

  }

}
