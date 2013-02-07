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
  
  $uri = JFactory::getUri(); 

	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
	} else {
		$menuItem = $menu->getItem($query['Itemid']);
	}
  
  if (!empty($query['s_kwds'])) {
    $segments[] = $query['s_kwds'];
    unset($query['s_kwds']);
  }
  
  if (!empty($query['arrival'])) {
    $segments[] = $query['arrival'];
    unset($query['arrival']);
  }
  
  if (!empty($query['departure'])) {
    $segments[] = $query['departure'];
    unset($query['departure']);
  } 
    
  if (!empty($query['occupancy'])) {
    $segments[] = $query['occupancy'];
    unset($query['occupancy']);
  }
  
  if (!empty($query['bedrooms'])) {
    $segments[] = $query['bedrooms'];
    unset($query['bedrooms']);
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
  
  
  // Need to loop over the segments and test each for a particular filter...
  
  // The first segment has to be the alias the search is based on.
  $vars['s_kwds'] = str_replace(':','-',$segments[0]);
  
  array_shift($segments);
  
  // The main filters will always come from the form
  // i.e. the dates, occupancy, bedrooms etc
  
  foreach($segments as $segment) {
    
    // We know that all filter will be _period_ separated, so let's explode on .
    $filter = explode('_',$segment);
    
    $vars[$filter[0]] = str_replace(':','-',$segment);
   
  }

	return $vars;
}
