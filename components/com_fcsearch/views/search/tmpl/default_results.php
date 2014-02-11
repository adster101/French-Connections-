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

$doc = JFactory::getDocument();

if (JDEBUG) {
  $_PROFILER = JProfiler::getInstance('Application');
}

JDEBUG ? $_PROFILER->mark('Start process search results template') : null;


$ordering = 'order_' . $this->state->get('list.sort_column') . '_' . $this->state->get('list.direction');

$sortFields = $this->getSortFields();
$s_kwds = $this->state->get('list.searchterm', '');
$pagdata = $this->pagination->getData();

if ($pagdata->next->link) {
  $doc->addHeadLink($pagdata->next->link, 'next', 'rel');
}

if ($pagdata->previous->link) {
  $doc->addHeadLink($pagdata->previous->link, 'prev', 'rel');
}

$refine_budget_min = $this->getBudgetFields();
$refine_budget_max = $this->getBudgetFields(250, 5000, 250, 'max_');

$min_budget = $this->state->get('list.min_price');
$max_budget = $this->state->get('list.max_price');

$searchterm = UCFirst(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));
$bedrooms = $this->state->get('list.bedrooms');
$occupancy = $this->state->get('list.occupancy');
$arrival = ($this->state->get('list.arrival', '')) ? JFactory::getDate($this->state->get('list.arrival'))->calendar('d-m-Y') : '';
$departure = ($this->state->get('list.departure', '')) ? JFactory::getDate($this->state->get('list.departure'))->calendar('d-m-Y') : '';

// Prepare the pagination string.  Results X - Y of Z
$start = (int) $this->pagination->get('limitstart') + 1;
$total = (int) $this->pagination->get('total');
$limit = (int) $this->pagination->get('limit') * $this->pagination->pagesTotal;
$limit = (int) ($limit > $total ? $total : $limit);
$pages = JText::sprintf('COM_FCSEARCH_TOTAL_PROPERTIES_FOUND', $total);
?>


<form id="property-search" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=en&Itemid=165&s_kwds=' . $s_kwds) ?>" method="POST" class="form-inline-header">
  <?php echo JHtml::_('form.token'); ?>
  <div class="row-fluid">
    <div class="well well-small no-bottom-margin clearfix">
      <div class="search-field">
        <label class="small" for="q">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
        </label>
        <input id="s_kwds" class="typeahead" type="text" name="s_kwds" autocomplete="Off" value="<?php echo $searchterm ?>"/>
      </div>
      <div class="search-field">
        <label class="small" for="arrival">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
        </label>
        <input type="text" name="arrival" id="arrival" size="30" value="<?php echo $arrival ?>" class="input-mini start_date small" autocomplete="Off" />
      </div>
      <div class="search-field">
        <label class="small" for="departure">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
        </label>
        <input type="text" name="departure" id="departure" size="30" value="<?php echo $departure ?>" class="end_date input-mini small" autocomplete="Off"/>
      </div>
      <div class="search-field">
        <label class="small" for="occupancy">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
        </label>
        <select id="occupancy" name="occupancy" class="span12 small">
          <?php echo JHtml::_('select.options', array('' => '...', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $occupancy); ?>
        </select>
      </div>
      <div class="search-field">
        <label class="small" for="bedrooms">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
        </label>
        <select id="bedrooms" name="bedrooms" class="span12">
          <?php echo JHtml::_('select.options', array('' => '...', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $bedrooms); ?>
        </select>
      </div>
      <div class="search-field">
        <label class="small" for="min_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MINIMUM_PRICE_RANGE'); ?></label>
        <select id="min_price" name="min" class="span12">
          <?php echo JHtml::_('select.options', $refine_budget_min, 'value', 'text', 'min_' . $min_budget); ?>
        </select>
      </div>
      <div class="search-field">
        <label class="small" for="max_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MAXIMUM_PRICE_RANGE'); ?></label>
        <select id="max_price" name="max" class="span12">
          <?php echo JHtml::_('select.options', $refine_budget_max, 'value', 'text', 'max_' . $max_budget); ?>
        </select>
      </div>
      <div class="search-field">
        <label for="sort_by" class="control-label small">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_SORT_BY'); ?>
        </label>
        <select id="sort_by" class="small span12" name="order">
          <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $ordering); ?>
        </select>
      </div>
      <button id="property-search-button" class="btn btn-primary btn-large pull-right" href="#">
        <i class="icon-search icon-white"> </i>
        <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
      </button>
    </div>    
    <input type="hidden" name="option" value="com_fcsearch" />
  </div>
</form>
<h1 class="small-h1">
  <?php echo $this->escape(str_replace(' - French Connections', '', $this->document->title)); ?>
</h1>
<div class="row-fluid">
  <div class="span-12">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#list" data-toggle="tab">List</a></li>
      <li><a href="#mapsearch" data-toggle="tab">Map</a></li>
      <li><a href="#localinfo" data-toggle="tab">Info</a></li>

    </ul>
  </div>
</div>
<div class="row-fluid">
  <div class="tab-content span9">
    <div class="tab-pane active" id="list">
      <?php if (count($this->results) > 0) : ?>
        <p class=""><?php echo $this->pagination->getResultsCounter(); ?></p>
        <ul class="search-results list-striped">
          <?php
          JDEBUG ? $_PROFILER->mark('Start process individual results (*10)') : null;

          for ($i = 0, $n = count($this->results); $i < $n; $i++) {
            $this->result = &$this->results[$i];
            if (!empty($this->result->id)) {
              echo $this->loadTemplate('result');
            }
          }
          JDEBUG ? $_PROFILER->mark('End process individual results (*10)') : null;
          ?>
        </ul>
      <?php else: ?>
        <p class='lead'>
          <strong><?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_HEADING'); ?></strong>
        </p>
        <p><?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_BODY'); ?></p>
        <?php
        // Load the most popular search module 
        $module = JModuleHelper::getModule('mod_popular_search');
        echo JModuleHelper::renderModule($module);
        ?>
      <?php endif; ?> 
      <div class="search-pagination">
        <div class="pagination">
          <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
      </div>

    </div>
    <div class="tab-pane" id="mapsearch">
      <div id="map_canvas"></div>
    </div>
    <div class="tab-pane" id="localinfo">
      <div class="row-fluid">
        <h2><?php echo $this->escape(($this->localinfo->title)); ?></h2>
        <?php echo $this->localinfo->description; ?>
      </div>
    </div>
  </div>
  <div class="span3">
    <?php
    JDEBUG ? $_PROFILER->mark('Start process refine') : null;
    echo $this->loadTemplate('refine');
    JDEBUG ? $_PROFILER->mark('End process refine') : null;
    ?>
  </div>
</div>






<!-- Modal -->
<div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_('COM_SHORTLIST_PLEASE_LOGIN') ?></h3>
  </div>
  <div class="modal-body">
    <div class="loading">Please wait...</div>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
<?php JDEBUG ? $_PROFILER->mark('End process search results template') : null; ?>

