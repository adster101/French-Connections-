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
    
    $query->select('id, user_id, property_id, date_created');
    $query->from('#__shortlist');
    $query->where('user_id = ' . (int) JFactory::getUser()->id); 
 
    $db->setQuery($query);
    
    return $query;
    
  }

}
