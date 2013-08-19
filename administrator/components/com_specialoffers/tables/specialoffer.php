<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.table');

// import the model helper lib
jimport('joomla.application.component.model');

/**
 * Hello Table class
 */
class SpecialOffersTableSpecialOffer extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__special_offers', 'id', $db);
	}
      
  public function load($pk = null, $reset = true) 
	{		
		if (parent::load($pk, $reset)) 
		{    
      // Set the title here so we can see what attribute we are editing
      JToolBarHelper::title(JText::_('Special Offer ('. $this->title.')'));
     
			return true;
		}
		else
		{
			return false;
		}
	}
  
  /*
   * Function get offer
   * Gets one special offer for a property 
   * 
   * params
   * @id; property id
   * 
   */
  public function getOffer($id = null) 
	{		
		$query = $this->_db->getQuery(true);
		$query->select('title,description');
		$query->from($this->_tbl);
    $query->where('unit_id = ' . $this->_db->quote($id));
    $query->where('published = 1');
    $query->where('start_date <= ' . $this->_db->Quote(date('Y-m-d')));
    $query->where('end_date >= ' . $this->_db->Quote(date('Y-m-d')));
            
    // Get the offer 
    $this->_db->setQuery($query,0,1);
    
    try
    {
      $result = $this->_db->loadObject();

      return $result;
    }
    catch (RuntimeException $e)
    {
      $je = new JException($e->getMessage());
      $this->setError($je);
      return false;
    }		
	}  
  
}
