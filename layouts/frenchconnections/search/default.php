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
    'filtersHidden' => false,
    'defaultLimit' => isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : JFactory::getApplication()->getCfg('list_limit', 20),
    'searchFieldSelector' => '#filter_search',
    'orderFieldSelector' => '#list_fullordering'
);

$data['options'] = array_unique(array_merge($customOptions, $data['options']));

$formSelector = !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm';

// Load search tools
JHtml::_('searchtools.form', $formSelector, $data['options']);
?>
<div class="js-stools clearfix well well-small">
 
    <div class="js-stools-container-list hidden-phone hidden-tablet">
      <?php echo JLayoutHelper::render('joomla.searchtools.default.list', $data); ?>
    </div>
  <!-- Filters div -->
  <div class="js-stools-container-filters hidden-phone clearfix">
    <?php echo JLayoutHelper::render('frenchconnections.search.default.filters', $data); ?>
  </div>

</div>