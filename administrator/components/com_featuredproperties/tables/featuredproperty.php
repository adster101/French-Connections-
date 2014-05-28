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
class FeaturedPropertiesTableFeaturedProperty extends JTable
{

  /**
   * Constructor
   *
   * @param object Database connector object
   */
  function __construct(&$db)
  {
    parent::__construct('#__featured_properties', 'id', $db);
  }

  /**
   * Basic check to ensure that there are no more than four FP for a given date.
   * 
   */
  public function check()
  {
    // Check for existing name
    $query = $this->_db->getQuery(true)
            ->select('count(*) as count')
            ->from($this->_db->quoteName('#__featured_properties'))
            ->where($this->_db->quoteName('start_date') . ' = ' . $this->_db->quote($this->start_date))
            ->where($this->_db->quoteName('featured_property_type') . ' = ' . $this->_db->quote($this->featured_property_type));
    $this->_db->setQuery($query);
    
    $result = $this->_db->loadObject();

    $count = (int) $result->count;
    
    if ($count >= 4)
    {
      $this->setError(JText::_('COM_FEATUREDPROPERTIES_MORE_THAN_FOUR'));
      return false;
    }
      
    return true;

  }

}
