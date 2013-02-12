<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.table');

/**
 * Hello Table class
 */
class HelloWorldTablePropertyAttributes extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__attributes_property', 'property_id', $db);
	}
	
	/**
	 * Overloaded load function
	 *
	 * @param       int $id primary key in this case
	 * @param       boolean $reset reset data
	 * @return      boolean
	 * @see JTable:load
	 */
	public function load($id = null, $reset = true) 
	{
    
    // Array to hold the result list
    $property_attributes = array();
    
    // Loads a list of the attributes that we are interested in
    // This is probably reused on the search part
		$query = $this->_db->getQuery(true);
		$query->select('at.field_name,pa.attribute_id');
		$query->from('#__attributes_property as pa');
    $query->leftJoin('#__attributes a on a.id = pa.attribute_id');

    $query->leftJoin('#__attributes_type at on at.id = a.attribute_type_id');

    $query->where($this->_db->quoteName('property_id') . ' = ' . (int) $id);
    $this->_db->setQuery($query);
    
		try
		{
      
      // Execute the db query, returns an iterator object.
			$result = $this->_db->getIterator();
      
      // Loop over the iterator and do stuff with it
      foreach ($result as $row){
        $tmp = JArrayHelper::fromObject($row);

        // If the facility type already exists
        if (!array_key_exists($tmp['field_name'], $property_attributes)) {
          $property_attributes[$tmp['field_name']] = array();
        }
        
        $property_attributes[$tmp['field_name']][] = $tmp['attribute_id'];
        
      }

      return $property_attributes;
		}
    
		catch (RuntimeException $e)
		{
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
	}
  
  /**
   * Overloaded save function
   * Takes the availability periods and saves them into the availability table.
   *  
   * 
   */
  public function save ($id = null, $attributes = array() ) 
  {
    
    
    if (!$this->check()) {
      JLog::add('JDatabaseMySQL::queryBatch() is deprecated.', JLog::WARNING, 'deprecated');
      return false;
      
    } else {

      // Firstly need to delete these...in a transaction would be better
      $query = $this->_db->getQuery(true);
      
      $query->delete('#__attributes_property')->where('property_id = ' . $id);
      
      
      $this->_db->setQuery($query);
      
			if (!$this->_db->execute())
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));
        print_r($this->_db->getErrorMsg());
				$this->setError($e);
				return false;
			}
      
      $query = $this->_db->getQuery(true);
      
      $query->insert('#__attributes_property');
      
			$query->columns(array('property_id','attribute_id'));
      
      foreach ($attributes as $attribute) {
        $insert_string = "$id," .$attribute."";
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
      JApplication::setUserState('com_helloworld.facilities.progress', true);
      
      
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
