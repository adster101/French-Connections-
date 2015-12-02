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
 * Filter HTML Behaviors for Finder.
 *
 * @package     Joomla.Site
 * @subpackage  com_finder
 * @since       2.5
 */
abstract class JHtmlRefine
{

    /**
     * Returns the HTML for a button to remove a filter applied via a query 
     * string parameter. E.g. ?lwl=true&offers=true
     * 
     * @param type $lwl
     * @return string
     */
    public static function removeQueryFilter($filter, $param, $text, $uri)
    {
        $html = '';

        if (!$filter)
        {
            return $html;
        }

        // Get the URI instance, handy!
        $uri = new JUri($uri);

        // Get the query string as an assoc array
        $query = $uri->getQuery(true);

        // Remove the passed in param e.g. lwl
        unset($query[$param]);

        // Set the query string 
        $uri->setQuery($query);

        // Make the uri a string again
        $uri = $uri->toString();
        $title = JText::_($text);

        return JHtmlRefine::getButton($uri, $title);
    }

    /**
     * Removes 'attribute' filters applied via a url parameter.
     * 
     * 
     *  
     * @param type $refine_options
     * @param type $uri
     * @param type $type
     * @return type
     */
    public static function removeAttributeFilters($refine_options, $uri, $type)
    {
        $html = '';
        $filters_to_remove = array();

        if (!empty($refine_options))
        {

            foreach ($refine_options as $filters)
            {
                if (!empty($filters))
                {
                    foreach ($filters as $filter => $value)
                    {
                        $value = (array) $value;

                        // Get an instance of the uri as a uri obj
                        $uriObj = new JUri($uri);

                        // Explode the path to an array - 'cos we don't want the 
                        // query string causing us problems
                        $tmp = array_filter(array_flip(explode('/', $uriObj->getPath())));

                        if ($type == 'accommodation_')
                        {
                            $filter_string = $type . $value['search_code'] . JApplication::stringURLSafe($value['title']) . '_' . $value['id'];
                        } else
                        {
                            $filter_string = $type . $value['search_code'] . JStringNormalise::toUnderscoreSeparated(JApplication::stringURLSafe($value['title'])) . '_' . $value['id'];
                        }
                        if (array_key_exists($filter_string, $tmp))
                        {
                            // Remove the filter from the url path
                            unset($tmp[$filter_string]);

                            // Turn the array back into a string
                            $new_path = '/' . implode('/', array_flip($tmp));

                            // Set the path in th uri obj to the new path
                            $uriObj->setPath($new_path);

                            $filters_to_remove[$filter]['url'] = $uriObj->toString();
                            $filters_to_remove[$filter]['filter'] = $value['title'];
                            $filters_to_remove[$filter]['count'] = $value['count'];
                        }
                    }
                }
            }

            if (count($filters_to_remove) > 0)
            {
                foreach ($filters_to_remove as $filter_to_remove)
                {
                    $html .= JHtmlRefine::getButton($filter_to_remove['url'], $filter_to_remove['filter']);
                }
            }

            return $html;
        }
    }

    public static function removeLocationFilters($refine_options, $uri, $type)
    {
        $html = '';
        $filters_to_remove = array();
        // Get the bread crumbs trail 
        $app = JFactory::getApplication();
        $pathway = $app->getPathway();
        $items = $pathway->getPathWay();


        foreach ($items as $key => $value)
        {

            if ($key > 0)
            {
                // TO DO - Make this into a function or sommat as it's repeated below.
                $tmp = explode('/', $uri); // Split the url out on the slash
                $filters = ($lang == 'en-GB') ? array_slice($tmp, 3) : array_slice($tmp, 4); // Remove the first 3 value of the URI
                $filters = (!empty($filters)) ? '/' . implode('/', $filters) : '';

                $filters_to_remove['url'] = $items[$key - 1]->link . $filters . $offers . $lwl;
                $filters_to_remove['filter'] = stripslashes(htmlspecialchars($value->name, ENT_COMPAT, 'UTF-8'));
            }
        }

        $html .= JHtmlRefine::getButton($filters_to_remove);

        return $html;
    }

    /**
     * Get a html fragment designed to 'remove' an applied filter
     * 
     * @param type $url
     * @param type $title
     * @return string
     */
    public static function getButton($url, $title)
    {

        $html .='<span>';
        {

            $html .='<a class="muted" href="' . JRoute::_($url) . '">';
            $html .='<span class="label label-warning"><i class=" glyphicon glyphicon-remove"> </i>&nbsp; ' . $title;
            $html .='</span></a>&nbsp;';
        }
        $html .='</span>';

        return $html;
    }

    public static function getRefineMapLink($layout = '')
    {
        $link = JURI::getInstance();

        $query = $link->getQuery(true);

        if ($layout)
        {
            $query['layout'] = $layout;
        } else
        {
            unset($query['layout']);
        }


        $link->setQuery($query);

        $link = $link->toString();

        return $link;
    }

}
