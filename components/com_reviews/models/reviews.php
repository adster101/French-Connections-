<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class ReviewsModelReviews extends JModelList {

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
    $user = JFactory::getUser();
    $user_id = $user->id;

    $query->select('a.*, b.unit_title');
    $query->from('#__reviews a');
    $query->leftJoin('#__unit_versions b on a.unit_id = b.unit_id');
    $query->where('a.created_by = ' . (int) $user_id);
    $query->where('b.review = 0');
    $db->setQuery($query);

    return $query;
  }

  

}
