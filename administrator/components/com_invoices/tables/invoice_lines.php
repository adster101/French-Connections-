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
class InvoicesTableInvoice_lines extends JTable
{

  /**
   * Constructor
   *
   * @param JDatabase A database connector object
   */
  public function __construct(&$db)
  {
    parent::__construct('#__invoice_lines', 'id', $db);
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
