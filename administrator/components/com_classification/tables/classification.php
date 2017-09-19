<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.tablenested');

/**
 * Hello Table class
 */
class ClassificationTableClassification extends JTableNested
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__classifications', 'id', $db);
	}	
  
 	public function bind($array, $ignore = '') 
  {

    // Check the root ID and create if not present
    $rootId = $this->getRootId();
    if ($rootId === false) {
      $rootId = $this->addRoot();
    }   

    
    
		return parent::bind($array, $ignore);
    
  }
  
  /**
	 * Override check function
	 *
	 * @return  boolean
	 *
	 * @see     JTable::check
	 * @since   11.1
	 */
	public function check()
	{
		// Check for a title.
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_CATEGORY'));
			return false;
		}
		$this->alias = trim($this->alias);
		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}

		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		return true;
	}
  
  
  public function store ( $updateNulls = false )
  {
 
    // Add more validation and what not here?
    
    
		$this->setLocation($this->parent_id, 'last-child');
    
    
    
    
		
		// Attempt to store the data.
		$return = parent::store();
    
    if ($return) {
      // Rebuild the path for this classification
      $this->rebuildPath($this->id);      
    }
    
    return $return;
        
    
  }
  




  
  
  /**
  * Add the root node to an empty table.
  *
  * @return    integer  The id of the new root node.
  */
  public function addRoot()
  {
      $db = JFactory::getDbo();
      $sql = 'INSERT INTO qitz3_classifications'
          . ' SET id = 1'
          . ', parent_id = 0'
          . ', lft = 0'
          . ', rgt = 1'
          . ', level = 0'
          . ', title = '.$db->quote( 'root' )
          . ', alias = '.$db->quote( 'root' )
          . ', access = 1'
          . ', published = 1'
          . ', path = '.$db->quote( '' )
          ;
      $db->setQuery( $sql );
      $db->query();

      return $db->insertid();
  }  
  
  
}
