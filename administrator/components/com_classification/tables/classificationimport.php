<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.tablenested');

/**
 * Hello Table class
 */
class ClassificationTableClassificationImport extends JTableNested
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
          . ', rgt = 0'
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
