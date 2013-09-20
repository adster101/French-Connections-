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
abstract class JHtmlRefine {

  /**
   * Method to generate fields for filtering dates
   *
   * @param   FinderIndexerQuery  $query    A FinderIndexerQuery object.
   * @param   array               $options  An array of options.
   *
   * @return  mixed  A rendered HTML widget on success, null otherwise.
   *
   * @since   2.5
   */
  public static function removeFilters($refine_options, $uri) {
    $html = '';
    $filter_counter = 1;
    $filters_to_remove = array();

    if (!empty($refine_options)) {

      foreach ($refine_options as $option => $filters) {
        foreach ($filters as $filter => $value) {
          

          $tmp = array_flip(explode('/', $uri));
          $remove = false;

          $filter_string = $value['search_code'] . JStringNormalise::toUnderscoreSeparated(JApplication::stringURLSafe($filter)) . '_' . $value['id'];

          if (array_key_exists($filter_string, $tmp)) {
            unset($tmp[$filter_string]);
            $new_uri = implode('/', array_flip($tmp));
            
            $filters_to_remove[$filter_counter]['url'] = $new_uri;
            $filters_to_remove[$filter_counter]['filter'] = $filter;
            $filters_to_remove[$filter_counter]['count'] = $value['count'];
          }
          
          // Increment the filter counter
          $filter_counter++;
        }
      }

      if (count($filters_to_remove) > 0) {

        $html .='<p>';
        foreach ($filters_to_remove as $filter_to_remove) {

          $html .='<a class="muted" href="' . JRoute::_('http://' . $filter_to_remove['url']) . '">';
          $html .='<span class="label"><i class="icon-delete"> </i>&nbsp; ' . $filter_to_remove['filter'];
          $html .='</span></a>&nbsp;';
        }
        $html .='</p><hr />';
      }

      return $html;
    }
  }

}