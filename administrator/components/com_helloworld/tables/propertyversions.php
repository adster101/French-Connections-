<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla nested table library
jimport('joomla.database.table');

// import the model helper lib
//jimport('joomla.application.component.model');

/**
 * Hello Table class
 */
class HelloWorldTablePropertyVersions extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__property_versions', 'parent_id', $db);
	}

  /*
   * Overridden store method to capture the created by and modified dates etc
   *
   *
   */
  public function store($updateNulls = false) {

    $date = JFactory::getDate();
    $user = JFactory::getUser();

    if ($this->id) {
      // Existing item
      $this->modified_on = $date->toSql();
      $this->modified_by = $user->get('id');

    } else {

      // New property version so add in the who created it and when

      if (empty($this->created_by)) {
        $this->created_by = $user->get('id');
      }

      if (empty($this->created_on)) {
        $this->created_on = $date->toSql();
      }
    }

    return parent::store($updateNulls);
  }


	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 *
	 * @link    http://docs.joomla.org/JTable/load
	 * @since   11.1
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function load($keys = null, $reset = true, $latest=true)
	{
		if (empty($keys))
		{
			// If empty, use the value of the current key
			$keyName = $this->_tbl_key;
			$keyValue = $this->$keyName;

			// If empty primary key there's is no need to load anything
			if (empty($keyValue))
			{
				return true;
			}

			$keys = array($keyName => $keyValue);
		}
		elseif (!is_array($keys))
		{
			// Load by primary key.
			$keys = array($this->_tbl_key => $keys);
		}

		if ($reset)
		{
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from($this->_tbl);
    $query->order('id desc');

		$fields = array_keys($this->getProperties());

		foreach ($keys as $field => $value)
		{
			// Check that $field is in the table.
			if (!in_array($field, $fields))
			{
				throw new UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
			}
			// Add the search tuple to the query.
			$query->where($this->_db->quoteName($field) . ' = ' . $this->_db->quote($value));
		}

		$this->_db->setQuery($query);

		$row = $this->_db->loadAssoc();

		// Check that we have a result.
		if (empty($row))
		{
			return false;
		}

		// Bind the object with the row and return.
		return $this->bind($row);
	}


}
