<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Helper for mod_search
 *
 * @package     Joomla.Site
 * @subpackage  mod_search
 * @since       1.5
 */
class modReSearchHelper {
 
    /**
   * Get a list of filter options for the state of a module.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  public static function getBudgetFields($start = 250, $end = 5000, $step = 250, $budget = 'min_', $select = 'COM_FCSEARCH_SEARCH_MINIMUM_PRICE_RANGE')
  {
    // Build the filter options.
    $options = array();

    $options[] = JHtml::_('select.option', '', JText::_($select));

    for ($i = $start; $i < $end; $i = $i + $step)
    {
      $options[] = JHtml::_('select.option', $budget . $i, $i);
    }

    return $options;
  }

}
