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
class modPopularSearchHelper
{
  /*
   * Get the list of regions alias so we can plug those into the search map - language aware!
   *
   * @return aww
   */

  public static function getPopularSearches($level = '')
  {
    $date = JHtml::date('-2 weeks', 'Y-m-d');
    $lang = JFactory::getLanguage()->getTag();

    // Get the list of regions, which are at level 2
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('count(*) as count, a.location as alias');
    $query->from($db->quoteName('#__search_log') . ' as a');

    if (!empty($level))
    {
      $query->join('left', $db->quoteName('#__classifications') . ' as c on c.id = a.location_id');
      $query->where('c.level = ' . (int) $level);
    }

    $query->where('a.location is not null');
    $query->where('a.date_created > ' . $db->quote($date));

    $query->group('a.location_id');
    $query->order('count desc');

    $db->setQuery($query, 0, 21);

    try
    {
      $searches = $db->loadObjectList();
    }
    catch (Exception $e)
    {
      // Log any exception
    }
    return $searches;
  }

}
