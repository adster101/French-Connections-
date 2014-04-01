<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla nested table library
jimport('joomla.database.table');

/**
 * Hello Table class
 */
class RentalTableTariffs extends JTable {

  public $id = '';
  public $start_date = '';
  public $end_date = '';
  public $tariff = '';

  /**
   * Constructor
   *
   * @param object Database connector object
   */
  function __construct(&$db) {
    parent::__construct('#__tariffs', 'unit_id', $db);
  }

  /**
   * Overloaded check function. This should sanity check the data we are about to insert.
   * Perhaps do this before deleting?
   * 
   * @return boolean 
   */
  public function check() {
    
    $start_date = JFactory::getDate($this->start_date)->toUnix();
    $end_date = JFactory::getDate($this->end_date)->toUnix();

    if ($start_date > $end_date) {
      return false;
    }
    
    if (!is_integer((int) $this->tariff)) {
      return false;
    }
    
    return true;
  }

}
