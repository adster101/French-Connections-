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

  static $menu;
	$segments = array();
   
  
	// Load the menu if necessary.
	if (!$menu)
	{
		$menu = JFactory::getApplication('site')->getMenu();
	}
  
  
  
  
	
  /*
	 * First, handle menu item routes first. When the menu system builds a
	 * route, it only provides the option and the menu item id. We don't have
	 * to do anything to these routes.
	 */
	if (count($query) === 2 && isset($query['Itemid']) && isset($query['option']))
	{
    
		return $segments;
	}

	/*
	 * Next, handle a route with a supplied menu item id. All system generated
	 * routes should fall into this group. We can assume that the menu item id
	 * is the best possible match for the query but we need to go through and
	 * see which variables we can eliminate from the route query string because
	 * they are present in the menu item route already.
	 */
	if (!empty($query['Itemid']))
	{
    
		// Get the menu item.
		$item = $menu->getItem($query['Itemid']);

    $segments[0] = '';
    $segments[1] = '';
    $segments[2] = '';
    $segments[3] = '';

		// Check if the search query string matches.
		if ($item && isset($query['q']))
		{
      $segments[0]=$query['q'];
			unset($query['q']);
		}

    
    
    // Check if the search query string matches.
		if ($item && isset($query['bedrooms']))
		{
      $segments[3]=$query['bedrooms'];
			unset($query['bedrooms']);
		}
        
		return $segments;
	}

	/*
	 * Lastly, handle a route with no menu item id. Fortunately, we only need
	 * to deal with the view as the other route variables are supposed to stay
	 * in the query string.
	 */
	if (isset($query['view']))
	{
		// Add the view to the segments.
		//$segments[] = $query['q'];
		//unset($query['view']);
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

  $segments[0] = str_replace(':','-', $segments[0]);
  
  
  $vars['q'] = $segments[0];
  $vars['bedrooms'] = $segments[1];
  
	
  
  

  
	return $vars;
}
