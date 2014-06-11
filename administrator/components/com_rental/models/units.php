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
class RentalModelUnits extends JModelList
{

  /**
   * Build an SQL query to load the list data.
   *
   * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
   *
   * @since   2.5
   */
  protected function getListQuery()
  {
    
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Select the required fields from the table.
    $query->select('a.unit_id, a.unit_title, a.property_id');
    $query->from('#__unit_versions AS a');
    $query->leftJoin('#__unit b on a.unit_id = b.id');

    // Filter by a single user.
    $property_id = (int) $this->getState('filter.search');

    $query->where('a.property_id = ' . $property_id );
    $query->where('b.published = 1 AND a.review = 0');

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
    parent::populateState();
  }

}
