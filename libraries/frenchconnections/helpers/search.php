<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * A set of helper functions for the property components for working out days until renewal 
 * general filters etc etc
 * 
 * @package frenchconnections
 * @subpackage library
 * 
 */
abstract class SearchHelper
{

  /**
   * Method to get the most appropriate menu item for the route based on the
   * supplied query needles.
   *
   * @param   array  $query  An array of URL parameters.
   *
   * @return  mixed  An integer on success, null otherwise.
   *
   * @since   2.5
   */
  public static function getItemid($query = array())
  {
    static $items, $active;

    // Get the menu items for com_finder.
    if (!$items || !$active)
    {
      $app = JFactory::getApplication('site');
      $com = $query[0];
      $menu = $app->getMenu();
      $items = $menu->getItems($query[0], $query[1]);
      $items = is_array($items) ? $items : array();
    }

    // Return the first item ID found. Might need to refine this is you want to link to more than one 
    // e.g. search page.
    return $items[0]->id;
  }
}