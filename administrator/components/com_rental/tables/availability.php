<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla nested table library
jimport('joomla.database.table');

/**
 * Hello Table class
 */
class RentalTableAvailability extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__availability', 'unit_id', $db);
	}

  /**
   * Overloaded save function
   * Takes the availability periods and saves them into the availability table.
   *
   *
   */
  public function save ($id = null, $availability_periods = array() )
  {
    if (!$this->check()) {
      JLog::add('JDatabaseMySQL::queryBatch() is deprecated.', JLog::WARNING, 'deprecated');
      return false;

    } else {

      $query = $this->_db->getQuery(true);

      $query->insert('#__availability');

			$query->columns(array('unit_id','start_date','end_date','availability'));

      foreach ($availability_periods as $period) {
        $insert_string = "$id,'" .$period['start_date']."','" . $period['end_date'] . "',". $period['status'] ."";
        $query->values($insert_string);
      }
			$this->_db->setQuery($query);

			if (!$this->_db->execute())
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));
				$this->setError($e);
				return false;
			}

      // Tick the availability progress flag to true
      JApplication::setUserState('com_rental.availability.progress', true);


      return true;
    }

  }

  /**
   * Overloaded check function. This should sanity check the data we are about to insert.
   * Perhaps do this before deleting?
   *
   * @return boolean
   */
  public function check() {
    return true;
  }
}
