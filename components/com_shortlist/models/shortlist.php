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
    
    $query->select('
      a.property_id as id,
      a.user_id, 
      a.property_id, 
      b.unit_title, 
      b.unit_id,
      c.expiry_date, 
      e.image_file_name as thumbnail, 
      f.title as property_type,
      g.title as accommodation_type,
      (single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms) as bedrooms,
      b.occupancy as occupancy,
      h.from_price as price,
      b.description,
      i.title as tariff_based_on,
      j.title as location_title,
      (select count(unit_id) from qitz3_reviews where unit_id = b.unit_id ) as reviews

      ');
    $query->from('#__shortlist a');
    // left join property etc
    $query->leftJoin('#__unit_versions b on b.unit_id = a.property_id');
    $query->leftJoin('#__property c on c.id = b.property_id');
    $query->leftJoin('#__property_versions d on d.property_id = c.id');
    $query->leftJoin('#__property_images_library e on e.version_id = b.id');
    $query->leftJoin('#__attributes f on f.id = b.property_type');
    $query->leftJoin('#__attributes g on g.id = b.accommodation_type');
    $query->leftJoin('#__unit h on h.id = b.unit_id');
    $query->leftJoin('#__attributes i on i.id = b.tariff_based_on');
    $query->leftJoin('#__classifications j on j.id = d.city');
    $query->where('a.user_id = ' . (int) JFactory::getUser()->id); 
    $query->where('b.review = 0'); 
    $query->where('d.review = 0'); 
    $query->where('e.ordering = 1');
    $query->order('a.date_created');
    
    $db->setQuery($query);
    
    return $query;
    
  }

}
