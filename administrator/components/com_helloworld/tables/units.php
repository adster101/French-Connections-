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
class HelloWorldTableUnits extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__property_units', 'id', $db);
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
	public function progress($pk = '', $reset = true)
	{
		
		if ($reset)
		{
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('
      (select count(*) from qitz3_attributes_property where property_id = ' . (int) $pk . ') as facilities,
      (select count(*) from qitz3_availability where id = ' . (int) $pk . ' and start_date > CURDATE()) as availability,
      (select count(*) from qitz3_tariffs where id =  ' . (int) $pk . ' and start_date > CURDATE()) as tariffs,
      (select count(*) from qitz3_property_images_gallery where property_id =  ' . (int) $pk . ') as images
    ');

   
		$this->_db->setQuery($query);

		$row = $this->_db->loadAssoc();

    // Check that we have a result.
		if (empty($row))
		{
			return false;
		}

		// Bind the object with the row and return.
		return $row;
	}
    
	/**
	 * 
   * 
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
	public function load_units($keys = null, $reset = true)
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
		$query->select('
        id,
        parent_id,
        ordering,
        unit_title,
        LEFT(description,050) as description,
        (select count(*) from qitz3_attributes_property where property_id = pu.id) as facilities,
        (select count(*) from qitz3_availability where id = pu.id and end_date > CURDATE()) as availability,
        (select count(*) from qitz3_tariffs where id = pu.id and end_date > CURDATE()) as tariffs,
        (select count(*) from qitz3_property_images_library where property_id =  pu.id) as images
      ');
		$query->from('#__property_units as pu');
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

		$rows = $this->_db->loadAssocList($key='id');

    // Check that we have a result.
		if (empty($rows))
		{
			return false;
		}
    
   
		return $rows;
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
      $this->modified = $date->toSql();
      $this->modified_by = $user->get('id');
    } else {
      // New newsfeed. A feed created and created_by field can be set by the user,
      // so we don't touch either of these if they are set.

      if (empty($this->created_by)) {
        $this->created_by = $user->get('id');
      }

      if (empty($this->created_on)) {
        $this->created_on = $date->toSql();
      }

    }
    
    return parent::store($updateNulls);
  }

  
}
