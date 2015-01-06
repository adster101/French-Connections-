<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$lang = JFactory::getLanguage();
$lang->load('com_accommodation', JPATH_SITE, null, false, true);
$lang->load('com_shortlist', JPATH_SITE, null, false, true);

$Itemid = SearchHelper::getItemid(array('component', 'com_realestatesearch'));

if (JDEBUG)
{
  $_PROFILER = JProfiler::getInstance('Application');
}

JDEBUG ? $_PROFILER->mark('Start process search results template') : null;


$ordering = 'order_' . $this->state->get('list.sort_column') . '_' . $this->state->get('list.direction');

$sortFields = $this->getSortFields();
$s_kwds = $this->state->get('list.searchterm', '');

// The layout for the anchor based navigation on the property listing
$search_data = new stdClass;
$search_data->searchterm = UCFirst(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));
$bedrooms = $this->state->get('list.bedrooms');
$refine_budget_min = $this->getBudgetFields(25000, 1500000, 50000, 'min_');
$refine_budget_max = $this->getBudgetFields(25000, 1500000, 50000, 'max_');

$min_budget = $this->state->get('list.min_price');
$max_budget = $this->state->get('list.max_price');
?>

<form class="form-inline" id="property-search" action="<?php echo JRoute::_('index.php?option=com_realestatesearch&lang=en&Itemid=' . $Itemid . '&s_kwds=' . $s_kwds) ?>" method="POST">

  <h1 class="small-h1 page-header">
    <?php echo $this->escape(str_replace(' - French Connections', '', $this->document->title)); ?>
  </h1>
  <div class="well well-sm well-light-blue clearfix form-inline">  
    <?php echo JHtml::_('form.token'); ?>
    <div class="form-group">
      <label class="sr-only" for="q">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
      </label>
      <input id="s_kwds" 
             class="typeahead search-box form-control" 
             type="text"
             name="s_kwds" 
             autocomplete="Off" 
             size="40"
             value="<?php echo $s_kwds ?>" 
             placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_DESTINATION_OR_PROPERTY') ?>" />
    </div>
    <div class="form-group">
      <label class="sr-only" for="bedrooms">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
      </label>
      <select id="bedrooms" name="bedrooms" class="form-control" >
        <?php echo JHtml::_('select.options', array('' => JText::_('COM_FCSEARCH_ACCOMMODATION_BEDROOMS'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $bedrooms); ?>
      </select>
    </div>
    <div class="form-group">
      <label class="sr-only" for="min_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MINIMUM_PRICE_RANGE'); ?></label>
      <select id="min_price" name="min" class="form-control">
        <?php echo JHtml::_('select.options', $refine_budget_min, 'value', 'text', 'min_' . $min_budget); ?>
      </select>
    </div>
    <div class="form-group">
      <label class="sr-only" for="max_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MAXIMUM_PRICE_RANGE'); ?></label>
      <select id="max_price" name="max" class="form-control">
        <?php echo JHtml::_('select.options', $refine_budget_max, 'value', 'text', 'max_' . $max_budget); ?>
      </select>
    </div>

    <button class="property-search-button btn btn-primary" href="#">
      <i class="icon-search icon-white"> </i>
      <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
    </button>
    <input type="hidden" name="option" value="com_realestatesearch" />
  </div>

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#list" data-toggle="tab"><i class="glyphicon glyphicon-list"></i>&nbsp;List</a></li>
        <li><a href="#mapsearch" data-toggle="tab"><i class="glyphicon glyphicon-map-marker"></i>&nbsp;Map</a></li>
        <li><a href="#localinfo" data-toggle="tab"><i class="glyphicon glyphicon-paperclip"></i>&nbsp;Info</a></li>
        <li class="visible-sm-inline-block visible-xs-inline-block pull-right">
          <a href="<?php echo JUri::getInstance()->toString() . '#refine' ?>" class="">  
            <span class="glyphicon glyphicon-filter"></span>
            <span class="hidden-xs">
              <?php echo JText::_('COM_FCSEARCH_FILTER_RESULTS'); ?>
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
  <div class="row">
    <div class="tab-content col-lg-9 col-md-9">
      <div class="tab-pane active" id="list">
        <?php if (count($this->results) > 0) : ?>
          <div class="clearfix">
            <p class="pull-left" style='line-height: 28px;'>
              <?php echo $this->pagination->getResultsCounter(); ?>
            </p>
            <div class="form-inline pull-right">
              <label for="sort_by" class="">
                <?php echo JText::_('COM_FCSEARCH_SEARCH_SORT_BY'); ?>
              </label>
              <select id="sort_by" class="form-control" name="order">
                <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $ordering); ?>
              </select>
            </div>
          </div>
          <div class="search-results list-unstyled clear">
            <?php
            JDEBUG ? $_PROFILER->mark('Start process individual results (*10)') : null;

            for ($i = 0, $n = count($this->results); $i < $n; $i++)
            {
              $this->result = &$this->results[$i];
              if (!empty($this->result->id))
              {
                echo $this->loadTemplate('result');
              }
            }
            JDEBUG ? $_PROFILER->mark('End process individual results (*10)') : null;
            ?>
          </div>
        <?php else: ?>
          <p class='lead'>
            <strong><?php echo JText::_('COM_REALESTATE_SEARCH_SEARCH_NO_RESULTS_HEADING'); ?></strong>
          </p>
          <p><?php echo JText::_('COM_REALESTATE_SEARCH_SEARCH_NO_RESULTS_BODY'); ?></p>
          <?php
          // Load the most popular search module 
          $module = JModuleHelper::getModule('mod_popular_realestate_search');
          echo JModuleHelper::renderModule($module);
          ?>
        <?php endif; ?> 
        <?php echo $this->pagination->getPagesLinks(); ?>

      </div>
      <div class="tab-pane" id="mapsearch">
        <div id="map_canvas"></div>
      </div>
      <div class="tab-pane" id="localinfo">
        <h2><?php echo $this->escape(($this->localinfo->title)); ?></h2>
        <?php echo $this->localinfo->description; ?>
      </div>
    </div>
    <div class="col-lg-3 col-md-3 refine-search">
      <?php
      JDEBUG ? $_PROFILER->mark('Start process refine') : null;
      echo $this->loadTemplate('refine');
      JDEBUG ? $_PROFILER->mark('End process refine') : null;
      ?>
    </div>

  </div>
</form>
<?php JDEBUG ? $_PROFILER->mark('End process search results template') : null; ?>

