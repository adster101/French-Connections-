<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
$document = JFactory::getDocument();
$app = JFactory::getApplication();

$document->addScript(JURI::root() . 'media/fc/js/jquery-ui-1.8.23.custom.min.js', 'text/javascript', true);
$document->addScript(JURI::root() . 'media/fc/js/date-range.js', 'text/javascript', true);

$document->addStyleSheet(JURI::root() . 'media/fc/css/jquery-ui-1.8.23.custom.css');
$bedrooms = $this->state->get('list.bedrooms');
$occupancy = $this->state->get('list.occupancy');
$start_date = $this->state->get('list.start_date');
$end_date = $this->state->get('list.end_date');
$searchterm = UCFirst(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));
?>

<form id="property-search" action="<?php echo JRoute::_(JURI::base() . 'index.php?option=com_fcsearch&lang=en') ?>" method="GET" class="form-vertical">
  <div class="row-fluid">
    <div class="well well-small clearfix">
      <div class="span4">
        <label for="q">
<?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
        </label>
        <input id="s_kwds" class="span12 typeahead" type="text" name="q" autocomplete="Off" value="<?php echo $searchterm ?>"/> 
      </div>
      <div class="span2">
        <label for="start_Date">
<?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
        </label>       
        <input type="text" name="start_date" id="start_date" size="30" value="<?php echo $start_date; ?>" class="start_date span9" autocomplete="Off" />
      </div>
      <div class="span2">
        <label for="end_date">
<?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
        </label>   
        <input type="text" name="end_date" id="end_Date" size="30" value="<?php echo $end_date; ?>" class="end_date span9" autocomplete="Off"/>
      </div>    
      <div class="span1">
        <label for="search_sleeps">
<?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>

        </label> 

        <select id="search_sleeps" class="span12" name="occupancy">
<?php echo JHtml::_('select.options', array(0 => '...', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $occupancy); ?>
        </select>
      </div>
      <div class="span1">
        <label for="search_bedrooms">
<?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
        </label>
        <select id="search_bedrooms" class="span12" name="bedrooms">
<?php echo JHtml::_('select.options', array(0 => '...', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $bedrooms); ?>
        </select>
      </div>

      <div class="span2">
        <button id="property-search-button" class="btn btn-large btn-primary pull-right" href="#">
          <i class="icon-search icon-white"> </i>
<?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
        </button>      
      </div>  
    </div>
  </div>
<?php
if ($this->total == 0):
  ?>
    <div id="search-result-empty">
      <h2><?php echo JText::_('COM_FINDER_SEARCH_NO_RESULTS_HEADING'); ?></h2>
  <?php if ($app->getLanguageFilter()) : ?>
        <p><?php echo JText::sprintf('COM_FINDER_SEARCH_NO_RESULTS_BODY_MULTILANG', $this->escape($this->query->input)); ?></p>
      <?php else : ?>
        <p><?php echo JText::sprintf('COM_FINDER_SEARCH_NO_RESULTS_BODY', $this->escape($this->query->input)); ?></p>
      <?php endif; ?>
    </div>


  <?php
else:
  // Prepare the pagination string.  Results X - Y of Z
  $start = (int) $this->pagination->get('limitstart') + 1;
  $total = (int) $this->pagination->get('total');
  $limit = (int) $this->pagination->get('limit') * $this->pagination->pagesTotal;
  $limit = (int) ($limit > $total ? $total : $limit);
  $pages = JText::sprintf('COM_FCSEARCH_TOTAL_PROPERTIES_FOUND', $total);
  ?>
    <div class="row-fluid">
      <div class="span9">
        <div class="search-pagination">
          <div class="pagination">

  <?php echo $this->pagination->getPagesLinks(); ?>
          </div>

        </div>
      </div>
      <div class="span2">
  <?php echo $this->pagination->getResultsCounter(); ?>
      </div>
      <div class="span1">
  <?php echo $this->pagination->getLimitBox(); ?>

      </div>
    </div>
<?php endif; ?>
  <input type="hidden" name="option" value="com_fcsearch" />
</form>
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">
      <?php echo JText::_('COM_FCSEARCH_SEARCH_PLEASE_ENTER_A_DESTINATION'); ?>
    </h3>
  </div>
  <div class="modal-body">
    <p>
     <?php echo JText::_('COM_FCSEARCH_SEARCH_PLEASE_ENTER_A_DESTINATION_BODY'); ?>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"> 
     <?php echo JText::_('COM_FCSEARCH_SEARCH_PLEASE_ENTER_A_DESTINATION_CLOSE'); ?>
  </button>
  </div>
</div>
