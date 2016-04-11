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
            //$app = JFactory::getApplication('site');
            $app = JApplicationSite::getInstance('site');

            $menu = $app->getMenu();

            // This set to retrieve menu items regardless of whether the user is logged in or not.
            $attributes = array($query[0], 'access');
            $values = array($query[1], array(1, 2, 3));

            $items = $menu->getItems($attributes, $values);
            $items = is_array($items) ? $items : array();
        }

        // Return the first item ID found. Might need to refine this is you want to link to more than one 
        // e.g. search page.
        return $items[0]->id;
    }

    /**
     * 
     */
    public static function isRealestateProperty($id = '')
    {

        // Need to look up unit id based on the id.
        // TO DO - Make this into a function...
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select('id');
        $query->from('#__realestate_property');
        $query->where('id = ' . (int) $id);

        $db->setQuery($query);

        try
        {
            $row = $db->loadObject();
        }
        catch (Exception $e)
        {
            return false;
        }

        return $row->id;
    }

    /**
     * Method which determines if there is a canconical version of this URL which
     * should be 301 redirected.
     */
    public static function isCanonical($location = '')
    {
        // Get the current URI that's being requeste4d
        $uri = JUri::getInstance();

        $path = $uri->getPath();
        $queryStr = $uri->getQuery();

        // Split the url 'segments' out into an array
        $filterStr = explode('/', $path);

        $filterArr = array_slice($filterStr, 3);

        $filtersStr_unsorted = implode('/', $filterArr);

        sort($filterArr);

        $filterStr_sorted = implode('/', $filterArr);
        
        // Compare the two strings...
        $compare = strcmp($filtersStr_unsorted, $filterStr_sorted);
        
        if ($compare <> 0)
        {
            $Itemid_search = SearchHelper::getItemid(array('component', 'com_fcsearch'));

            $route = 'index.php?option=com_fcsearch&Itemid=' . $Itemid_search . '&s_kwds=' .
                    $location . '/' . $filterStr_sorted . '?' . $queryStr;

            // And redirect
            header('Location: ' . JRoute::_($route));
            exit;
        }
    }

}
