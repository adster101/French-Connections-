<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Helper for mod_search
 *
 * @package     Joomla.Site
 * @subpackage  mod_search
 * @since       1.5
 */
class modFcSearchHelper {
  /*
   * Get the list of regions alias so we can plug those into the search map - language aware!
   *
   * @return aww
   */

  public static function getSearchRegions() {

    $input = JFactory::getApplication()->input;

    $lang = $input->get('lang', 'en');

    // Get the list of regions, which are at level 2
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('id,alias, title');

    if ($lang == 'fr') {
      $query->from($db->quoteName('#__classifications_translations') . ' AS t');
    } else {
      $query->from($db->quoteName('#__classifications') . ' AS t');
    }

    $query->where('level = 3');

    $db->setQuery($query);

    try {
      $regions = $db->loadObjectList($key = 'id');
    } catch (Exception $e) {
      // Log any exception
    }
    return $regions;
  }

  /*
   * Get the list of regions alias so we can plug those into the search map - language aware!
   *
   * @return aww
   */

  public static function getPopularSearches() {


    $lang = JFactory::getLanguage()->getTag();

    // Get the list of regions, which are at level 2
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('count(*) as count, c.title, c.alias');
    $query->from($db->quoteName('#__search_log') . ' as a');    

    if ($lang == 'fr-FR') {
      $query->join('left',$db->quoteName('#__classifications_translations') . ' on c.id = a.location_id');
    } else {
      $query->join('left',$db->quoteName('#__classifications') . ' as c on c.id = a.location_id');
    }
    $query->group('a.location_id');
    $query->order('count desc');
      $query->where('c.title is not null');
  
    $db->setQuery($query, 0,8);

    try {
      $searches = $db->loadObjectList();
    } catch (Exception $e) {
      // Log any exception
    }
    return $searches;
  }

}
