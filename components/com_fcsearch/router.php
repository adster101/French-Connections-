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
function FcSearchBuildRoute(&$query)
{
  
	$segments = array();

  // get a menu item based on Itemid or currently active
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();

	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
	} else {
		$menuItem = $menu->getItem($query['Itemid']);
	}
  
  $segments[] = $menuItem->alias;

  if (!empty($query['q'])) {
    $segments[] = $query['q'];
    unset($query['q']);
  }
    
  if (!empty($query['bedrooms'])) {
    $segments[] = $query['bedrooms'];
    unset($query['bedrooms']);
  }
  
  if (!empty($query['occupancy'])) {
    $segments[] = 'occupancy_'.$query['occupancy'];
    unset($query['occupancy']);
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
function FcSearchParseRoute($segments)
{
  
  
  $vars = array();
  $app = JFactory::getApplication();
  $menu = $app->getMenu();

  
  // Count segments
  $count = count( $segments );

  $vars['view'] = $menu->getActive()->query['view'];
  
  $vars['q'] = str_replace(':','-',$segments[0]);

  if (isset($segment[1])) {
    $vars['bedrooms'] = $segments[1];
  }

	return $vars;
}
