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
  public static function removeLWLFilter($lwl)
  {
    $html = '';

    if (!$lwl)
    {
      return $html;
    }
    $uri = JUri::getInstance()->toString(array('user', 'pass', 'host', 'port', 'path', 'fragment'));

    $html .='<span>';
    $html .='<a class="muted" href="' . JRoute::_('http://' . $uri) . '">';
    $html .='<span class="label label-warning"><i class=" glyphicon glyphicon-remove"></i>Long Winter Let';
    $html .='</span></a>&nbsp;';

    $html .='</span>';


    return $html;
  }
  
  /**
   * 
   */
  public static function removeOffersFilter($offers)
  {
    $html = '';

    if (!$offers)
    {
      return $html;
    }
    $uri = JUri::getInstance()->toString(array('user', 'pass', 'host', 'port', 'path', 'fragment'));

    $html .='<span>';


    $html .='<a class="muted" href="' . JRoute::_('http://' . $uri) . '">';
    $html .='<span class="label label-warning"><i class=" glyphicon glyphicon-remove"></i>Special offers';
    $html .='</span></a>&nbsp;';

    $html .='</span>';


    return $html;
  }

  /**
   * 
   */
  public static function removeAttributeFilters($refine_options, $uri)
  {
    $html = '';
    $filter_counter = 1;
    $filters_to_remove = array();

    if (!empty($refine_options))
    {

      foreach ($refine_options as $option => $filters)
      {
        if (!empty($filters))
        {
          foreach ($filters as $filter => $value)
          {
            $tmp = array_flip(explode('/', $uri));

            $remove = false;

            $filter_string = $value['search_code'] . JStringNormalise::toUnderscoreSeparated(JApplication::stringURLSafe($value['title'])) . '_' . $value['id'];

            if (array_key_exists($filter_string, $tmp))
            {
              unset($tmp[$filter_string]);
              $new_uri = implode('/', array_flip($tmp));

              $filters_to_remove[$filter_counter]['url'] = $new_uri;
              $filters_to_remove[$filter_counter]['filter'] = $value['title'];
              $filters_to_remove[$filter_counter]['count'] = $value['count'];
            }

            // Increment the filter counter
            $filter_counter++;
          }
        }
      }

      if (count($filters_to_remove) > 0)
      {

        $html .='<span>';
        foreach ($filters_to_remove as $filter_to_remove)
        {

          $html .='<a class="muted" href="' . JRoute::_('http://' . $filter_to_remove['url']) . '">';
          $html .='<span class="label label-warning"><i class=" glyphicon glyphicon-remove"> </i>&nbsp; ' . $filter_to_remove['filter'];
          $html .='</span></a>&nbsp;';
        }
        $html .='</span>';
      }

      return $html;
    }
  }

  public static function removeTypeFilters($refine_options, $uri, $type)
  {
    $html = '';
    $filter_counter = 1;
    $filters_to_remove = array();

    if (!empty($refine_options))
    {

      foreach ($refine_options as $filter => $value)
      {
        $tmp = array_flip(explode('/', $uri));

        $remove = false;

        $filter_string = $type . JStringNormalise::toDashSeparated(JApplication::stringURLSafe($value->title)) . '_' . $value->id;

        if (array_key_exists($filter_string, $tmp))
        {
          unset($tmp[$filter_string]);
          $new_uri = implode('/', array_flip($tmp));

          $filters_to_remove[$filter_counter]['url'] = $new_uri;
          $filters_to_remove[$filter_counter]['filter'] = $value->title;
          $filters_to_remove[$filter_counter]['count'] = $value->count;
        }

        // Increment the filter counter
        $filter_counter++;
      }



      if (count($filters_to_remove) > 0)
      {

        $html .='<span>';
        foreach ($filters_to_remove as $filter_to_remove)
        {

          $html .='<a class="muted" href="' . JRoute::_('http://' . $filter_to_remove['url']) . '">';
          $html .='<span class="label label-warning"><i class=" glyphicon glyphicon-remove"> </i>&nbsp; ' . $filter_to_remove['filter'];
          $html .='</span></a>&nbsp;';
        }
        $html .='</span>';
      }

      return $html;
    }
  }

}