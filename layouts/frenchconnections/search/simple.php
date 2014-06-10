<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

// Set some basic options
$customOptions = array(
    'filtersHidden' => isset($data['options']['filtersHidden']) ? $data['options']['filtersHidden'] : empty($data['view']->activeFilters),
    'defaultLimit' => isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : JFactory::getApplication()->getCfg('list_limit', 20),
    'searchFieldSelector' => '#filter_search',
    'orderFieldSelector' => '#list_fullordering'
);

$data['options'] = array_unique(array_merge($customOptions, $data['options']));

$formSelector = !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm';

// Load search tools
//JHtml::_('searchtools.form', $formSelector, $data['options']);
?>
<div class="js-stools clearfix well well-small">
  <div class="clearfix">
    <div class="js-stools-container-bar">
      <?php //echo JLayoutHelper::render('frenchconnections.search.simple.bar', $data);  ?>
    </div>
    <div class="js-stools-container-list">
      <?php echo JLayoutHelper::render('frenchconnections.search.simple.list', $data); ?>
    </div>
  </div>
  <!-- Filters div -->
  <div class="js-stools-container-filters">
    <?php echo JLayoutHelper::render('frenchconnections.search.simple.filters', $data); ?>
	
  </div>
</div>