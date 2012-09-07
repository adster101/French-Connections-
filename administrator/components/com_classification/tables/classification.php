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

    $rootId = $this->getRootId();
if ($rootId === false) {
    $rootId = $this->addRoot();
}   

    // Bind the rules.
    if (isset($array['rules']) && is_array($array['rules']))
    {
            $rules = new JAccessRules($array['rules']);
            $this->setRules($rules);
    }
    
		return parent::bind($array, $ignore);
    
  }
  
  public function store (  )
  {
 
    // Add more validation and what not here?
    
		$this->setLocation($this->parent_id, 'last-child');
		
		// Attempt to store the data.
		return parent::store();
    
  }
  
  /**
  * Method to compute the default name of the asset.
  * The default name is in the form `table_name.id`
  * where id is the value of the primary key of the table.
  *
  * @return      string
  * @since       2.5
  */
  protected function _getAssetName()
  {
    $k = $this->_tbl_key;
    return 'com_classification.classification.'.(int) $this->$k;
  }

  /**
    * Method to return the title to use for the asset table.
    *
    * @return      string
    * @since       2.5
    */
  protected function _getAssetTitle()
  {
    return $this->title;
  }

  /**
    * Method to get the asset-parent-id of the item
    *
    * @return      int
    */
  protected function _getAssetParentId()
  {
    // We will retrieve the parent-asset from the Asset-table
    $assetParent = JTable::getInstance('Asset');

    // Default: if no asset-parent can be found we take the global asset
    $assetParentId = $assetParent->getRootId();

    // Find the parent-asset
    if (($this->catid)&& !empty($this->catid))
    {
            // The item has a category as asset-parent
            $assetParent->loadByName('com_classification.classification.' . (int) $this->id);
    }
    else
    {
            // The item has the component as asset-parent
            $assetParent->loadByName('com_classification');
    }

    // Return the found asset-parent-id
    if ($assetParent->id)
    {
            $assetParentId=$assetParent->id;
    }

    return $assetParentId;
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
