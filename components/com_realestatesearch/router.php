<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * Method to build a SEF route.
 *
 * @param   array  &$query  An array of route variables.
 *
 * @return  array  An array of route segments.
 *
 * @since   2.5
 */
function RealestateSearchBuildRoute(&$query)
{

  $segments = array();

  if (!empty($query['s_kwds']))
  {
    $segments[] = $query['s_kwds'];
    unset($query['s_kwds']);
  }

  if (!empty($query['bedrooms']))
  {
    $segments[] = $query['bedrooms'];
    unset($query['bedrooms']);
  }

  if (!empty($query['order']))
  {
    $segments[] = $query['order'];
    unset($query['order']);
  }

  if (!empty($query['min']))
  {
    $segments[] = $query['min'];
    unset($query['min']);
  }

  if (!empty($query['max']))
  {
    $segments[] = $query['max'];
    unset($query['max']);
  }

  return $segments;
}

/**
 * Method to parse a SEF route.
 *
 * @param   array  $segments  An array of route segments.
 *
 * @return  array  An array of route variables.
 *
 * @since   2.5
 */
function RealestateSearchParseRoute($segments)
{

  $vars = array();
  $app = JFactory::getApplication();
  $menu = $app->getMenu();

  // Count segments
  $count = count($segments);

  // The first segment has to be the alias the search is based on.
  $vars['s_kwds'] = str_replace(':', '-', $segments[0]);

  array_shift($segments);

  // The main filters come from the search form and the search filters that may be applied
  // i.e. the dates, occupancy, bedrooms, property type, activities, facilities etc
  // Loop over each remaining segment
  foreach ($segments as $segment)
  {

    // We know that all filter will be _ separated, so let's explode it into an array .
    $filter = explode('_', $segment);

    // If the array key already exisis, then it means there are more than one filter of this type being applied in this search
    if (array_key_exists($filter[0], $vars))
    {

      // Take the existing element(s) - as we know there are one or more overall in the search
      $existing = $vars[$filter[0]];

      // If the existing filters are already an array we add the new one to the end
      if (is_array($existing))
      {

        array_push($existing, $segment);

        // And set the filter to the array
        $vars[$filter[0]] = $existing;
      }
      else
      {
        // Otherwise, we know there was only one existing filter of this type so we generate an array
        $vars[$filter[0]] = array($existing, $segment);
      }
    }
    else
    { // This filter is only applied once (e.g. start date, bedrooms etc)
      // Save it in the $vars array any way...
      $vars[$filter[0]] = str_replace(':', '-', $segment);
    }
  }
  return $vars;
}
