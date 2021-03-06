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
class InvoicesTableInvoice extends JTable
{

  /**
   * Constructor
   *
   * @param JDatabase A database connector object
   */
  public function __construct(&$db)
  {
    parent::__construct('#__invoices', 'id', $db);
  }

  /**
   * Override this puppy
   *
   * Method to store a row in the database from the JTable instance properties.
   * If a primary key value is set the row with that primary key value will be
   * updated with the instance property values.  If no primary key value is set
   * a new row will be inserted into the database with the properties from the
   * JTable instance.
   *
   * @param   boolean  $updateNulls  True to update fields even if they are null.
   *
   * @return  boolean  True on success.
   *
   * @link    http://docs.joomla.org/JTable/store
   * @since   11.1
   */
  public function store($updateNulls = false)
  {
    $result = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_keys[0]);

    return $result;
  }

  /**
   * Method to reset class properties to the defaults set in the class
   * definition. It will ignore the primary key as well as any private class
   * properties (except $_errors).
   *
   * @return  void
   *
   * @link    http://docs.joomla.org/JTable/reset
   * @since   11.1
   */
  public function reset()
  {
    // Get the default values for the class from the table.
    foreach ($this->getFields() as $k => $v)
    {
      $this->$k = $v->Default;
    }

    // Reset table errors
    $this->_errors = array();
  }



}
