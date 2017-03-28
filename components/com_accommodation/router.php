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
function AccommodationBuildRoute(&$query)
{

  $segments = array();
  // get a menu item based on Itemid or currently active

  $app = JFactory::getApplication();
  $menu = $app->getMenu();

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
function AccommodationParseRoute($segments)
{
  $vars = array();
  $app = JFactory::getApplication();
  $lang = JFactory::getLanguage();
  $lang->load('com_accommodation');
  JLoader::register('SearchHelper', JPATH_LIBRARIES . '/frenchconnections/helpers/search.php');

  $accommodation_itemid = SearchHelper::getItemid(array('component', 'com_accommodation'));
  $realestate_itemid = SearchHelper::getItemid(array('component', 'com_realestate'));

  // 
  $vars['id'] = (int) $segments[0];

  $input = $app->input;

  $unit_id = $input->get('unit_id', '', 'int');

  if (empty($unit_id))
  {

    // Need to look up unit id based on the id.
    // TO DO - Make this into a function...
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('id');
    $query->from('#__unit');
    $query->where('property_id = ' . (int) $segments[0]);
    $query->where('ordering = 1');
    $query->where('published = 1');
    $db->setQuery($query);

    $unit = $db->loadObject();

    if (empty($unit))
    {

      // Look up the ID to see if we're dealing with a realestate property
      if (SearchHelper::isRealestateProperty($segments[0]))
      {
        // Work out the link and redirect
        $link = 'index.php?option=com_realestate&Itemid=' . $realestate_itemid . '&id=' . (int) $segments[0];
        $app->redirect(JRoute::_($link, true));
      }

      throw new Exception(JText::_('WOOT'), 404);
    }

    // Redirect to the correct URL
    $link = 'index.php?option=com_accommodation&Itemid=' . $accommodation_itemid . '&id=' . (int) $segments[0] . '&unit_id=' . (int) $unit->id;

    $app->redirect(JRoute::_($link, true));
  }

  return $vars;
}

