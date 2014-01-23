<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class ShortlistModelShortlist extends JModelList {

  /**
   * @var object item
   */
  protected $items;

  /*
   * Method to build out a query which, when executed, will return a the property shortlist
   *
   * @return  JDatabaseQuery  A database query.
   *
   * @since   2.5
   *
   */
  protected function getListQuery() {
    
    $db = JFactory::getDbo();
    
    $query = $db->getQuery(true);
    
    $query->select('a.user_id, a.property_id, a.date_created, b.unit_title');
    $query->from('#__shortlist a');
    // left join property etc
    $query->leftJoin('#__unit_versions b on b.property_id = a.property_id');
    $query->where('a.user_id = ' . (int) JFactory::getUser()->id); 
    $query->where('b.review = 0'); 
 
    $db->setQuery($query);
    
    return $query;
    
  }

}
