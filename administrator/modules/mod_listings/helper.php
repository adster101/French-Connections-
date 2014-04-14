<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_latest
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Helper for mod_latest
 *
 * @package     Joomla.Administrator
 * @subpackage  mod_latest
 * @since       1.5
 */
abstract class ModListingHelper {

  /**
   * Get a list of properties owned by the logged in user.
   *
   * @param   JRegistry  &$params  The module parameters.
   *
   * @return  mixed  An array of articles, or false on error.
   */
  public static function getList(&$params) {
    $user = JFactory::getuser();
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    // Select some fields
    $query->select('
      a.id,
      d.id as unit_id,
      b.title,
      a.checked_out,
      a.checked_out_time,
      a.created_by,
      a.published,
      date_format(a.expiry_date, "%D %M %Y") as expiry_date,
      date_format(a.created_on, "%D %M %Y") as created_on,
      date_format(a.modified, "%D %M %Y") as modified,
      a.VendorTxCode,
      a.review,
      d.id as unit_id,
      f.image_file_name as thumbnail
    ');

    $query->where('a.created_by=' . (int) $user->id);
      
    $query->from('#__property as a');
    $query->join('inner', '#__property_versions as b on (
      a.id = b.property_id
      and b.id = (select max(c.id) from #__property_versions c where c.property_id = a.id)
    )');

    // Join the units for the image
    $query->join('left', '#__unit d on d.property_id = a.id');
    $query->join('left', '#__unit_versions e on (d.id = e.unit_id and e.id = (select max(f.id) from #__unit_versions f where unit_id = d.id))');
    $query->where('(d.ordering = 1 or d.ordering is null)');

    // Join the images, innit!
    $query->join('left', '#__property_images_library f on e.id = f.version_id');
    $query->where('(f.ordering = 1 or f.ordering is null)');

    $db->setQuery($query);
    
    try {
      $items = $db->loadObjectList();
    } catch (Exception $e) {
      return false;
    }

    return $items;
  }

}
