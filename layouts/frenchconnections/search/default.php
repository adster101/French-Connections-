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
$filters = $data['view']->filterForm->getGroup('filter');

// Load search tools
//JHtml::_('searchtools.form', $formSelector, $data['options']);
?>
<div class="js-stools clearfix">
  <!-- Filters div --> 
  <div class="js-stools-container-filters clearfix">

    <label for="filter_search" class="element-invisible">
      <?php echo JText::_('JSEARCH_FILTER'); ?>
    </label>
    <?php if (!empty($filters['filter_search'])) : ?>
      <div class="btn-wrapper input-append">
        <?php echo $filters['filter_search']->input; ?>
        <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
          <i class="icon-search"></i>
        </button>
      </div>
    <?php endif; ?>
    <div class="js-stools-container-list hidden-phone hidden-tablet">

      <?php echo JLayoutHelper::render('joomla.searchtools.default.list', $data); ?>
    </div>
	<div class="js-stools-container-filters hidden-phone clearfix">
		<?php echo JLayoutHelper::render('joomla.searchtools.default.filters', $data); ?>
	</div>
    <div class="btn-wrapper">
      <button type="button" class="btn hasTooltip js-stools-btn-clear" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>">
        <?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
      </button>
    </div>
  </div>
</div>
<hr />