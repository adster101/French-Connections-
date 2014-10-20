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
 * User notes table class
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class NotesTableNote extends JTable
{

  /**
   * Constructor
   *
   * @param  JDatabaseDriver  &$db  Database object
   *
   * @since  2.5
   */
  public function __construct(&$db)
  {
    parent::__construct('#__listing_notes', 'id', $db);
  }

  /**
   * Overloaded store method for the notes table.
   *
   * @param   boolean  $updateNulls  Toggle whether null values should be updated.
   *
   * @return  boolean  True on success, false on failure.
   *
   * @since   2.5
   */
  public function store($updateNulls = false)
  {
    $date = JFactory::getDate()->toSql();
    $userId = JFactory::getUser()->id;

    if (empty($this->id))
    {
      // New record.
      $this->created_by = $userId;
    }

    // Attempt to store the data.
    return parent::store($updateNulls);
  }

}
