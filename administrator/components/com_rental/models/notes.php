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
class RentalModelNotes extends JModelList {

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
    $section = $this->getState('filter.category_id');


    $query->from('#__property_notes AS a');

    // Filter by a single user.
    $property_id = (int) $this->getState('filter.property_id');

    // Select the required fields from the table.
    $query->select('
        a.id,
        a.subject,
        a.created_time,
        a.property_id,
        a.body
        ');

    $query->where('a.property_id = ' . $property_id);

    // Add the list ordering clause.
    $orderCol = $this->state->get('list.ordering');
    $orderDirn = $this->state->get('list.direction');

    $category = $this->state->get('filter.category_id', '');

    if (!empty($category)) {
      $query->where('(a.catid = ' . $category . ' OR c.property_id = ' . $category . ')');
    }

    $query->order($db->escape($orderCol . ' ' . $orderDirn));


    return $query;
  }

  /**
   * Method to get a store id based on model configuration state.
   *
   * This is necessary because the model is used by the component and
   * different modules that might need different sets of data or different
   * ordering requirements.
   *
   * @param   string  $id  A prefix for the store id.
   *
   * @return  string  A store id.
   *
   * @since   2.5
   */
  protected function getStoreId($id = '') {
    // Compile the store id.
    $id .= ':' . $this->getState('filter.search');
    $id .= ':' . $this->getState('filter.state');
    $id .= ':' . $this->getState('filter.category_id');

    return parent::getStoreId($id);
  }

  /**
   * Gets a user object if the user filter is set.
   *
   * @return  JUser  The JUser object
   *
   * @since   2.5
   */
  public function getUser() {
    $user = new JUser;

    // Filter by search in title
    $search = JFactory::getApplication()->input->get('u_id', 0, 'int');
    if ($search != 0) {
      $user->load((int) $search);
    }

    return $user;
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
    parent::populateState('a.created_time', 'DESC');
    $app = JFactory::getApplication();
    $input = $app->input;

    // Adjust the context to support modal layouts.
    if ($layout = $input->get('layout')) {
      $this->context .= '.' . $layout;
    }

    $this->setState('list.limit', 50);
    
    $value = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
    $this->setState('filter.search', $value);

    $published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
    $this->setState('filter.state', $published);

    $section = $app->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
    $this->setState('filter.category_id', $section);

    $userId = $input->get('property_id', 0, 'int');
    $this->setState('filter.property_id', $userId);

  }

}
