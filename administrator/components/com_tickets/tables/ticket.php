<?php

/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// No direct access
defined('_JEXEC') or die;

/**
 * invoice Table class
 */
class TicketsTableTicket extends JTable {

  /**
   * Constructor
   *
   * @param JDatabase A database connector object
   */
  public function __construct(&$db) {
    parent::__construct('#__tickets', 'id', $db);
  }

  public function store($updateNulls = false) {
    
    $user = JFactory::getUser();
    $date = JFactory::getDate();
    
    if (empty($this->created_by)) {
      $this->created_by = $user->get('id');
    }
    
    if (empty($this->date_created)) {
      $this->date_created = $date->toSql();
    }
    return parent::store($updateNulls);
  }

  /**
   * Method to set the publishing state for a row or list of rows in the database
   * table.  The method respects checked out rows by other users and will attempt
   * to checkin rows that it can after adjustments are made.
   *
   * @param	mixed	An optional array of primary key values to update.  If not
   * 					set the instance property value is used.
   * @param	integer The publishing state. eg. [0 = unpublished, 1 = published]
   * @param	integer The user id of the user performing the operation.
   * @return	boolean	True on success.
   * @since	1.6
   */
  public function publish($pks = null, $state = 1, $userId = 0) {
    $k = $this->_tbl_key;

    // Sanitize input.
    JArrayHelper::toInteger($pks);
    $userId = (int) $userId;
    $state = (int) $state;

    // If there are no primary keys set check to see if the instance key is set.
    if (empty($pks)) {
      if ($this->$k) {
        $pks = array($this->$k);
      }
      // Nothing to set publishing state on, return false.
      else {
        $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
        return false;
      }
    }

    // Build the WHERE clause for the primary keys.
    $where = $k . ' IN (' . implode(',', $pks) . ')';

    // Update the publishing state for rows with the given primary keys.
    $this->_db->setQuery(
            'UPDATE ' . $this->_db->quoteName($this->_tbl) .
            ' SET ' . $this->_db->quoteName('state') . ' = ' . (int) $state .
            ' WHERE (' . $where . ')'
    );

    try {
      $this->_db->execute();
    } catch (RuntimeException $e) {
      $this->setError($e->getMessage());
      return false;
    }

    // If the JTable instance value is in the list of primary keys that were set, set the instance.
    if (in_array($this->$k, $pks)) {
      $this->state = $state;
    }

    $this->setError('');
    return true;
  }

}
