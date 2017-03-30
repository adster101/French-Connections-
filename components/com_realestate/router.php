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
function RealestateBuildRoute(&$query)
{

  $segments = array();
  // get a menu item based on Itemid or currently active
  $app = JFactory::getApplication();
  $menu = $app->getMenu();

  $uri = JFactory::getUri();

  if (empty($query['Itemid']))
  {
    $menuItem = $menu->getActive();
  }
  else
  {
    $menuItem = $menu->getItem($query['Itemid']);
  }

  if (!empty($query['id']))
  {
    $segments[] = $query['id'];
    unset($query['id']);
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
function RealestateParseRoute($segments)
{

  $vars = array();
  $app = JFactory::getApplication();
  $lang = JFactory::getLanguage();
  $lang->load('com_realestate');

  // The first segment has to be the alias the search is based on.
  $vars['id'] = (int) $segments[0];

  return $vars;
}

