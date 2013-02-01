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

$document = JFactory::getDocument();
$app = JFactory::getApplication();


$bedrooms = $this->state->get('list.bedrooms');
$occupancy = $this->state->get('list.occupancy');
$start_date = $this->state->get('list.start_date');
$end_date = $this->state->get('list.end_date');
$searchterm = UCFirst(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));
?>
<div class="finder">
  <h1>
    <small><?php echo $this->document->title; ?></small>
  </h1>

  <div id="search-form" >
    <form id="property-search" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=en&Itemid=165') ?>" method="GET" class="form-vertical">
      <div class="row-fluid">
        <div class="well well-small clearfix">
          <div class="span4">
            <label class="small" for="q">
              <?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
            </label>
            <input id="s_kwds" class="span12 typeahead" type="text" name="q" autocomplete="Off" value="<?php echo $searchterm ?>"/> 
          </div>
          <div class="span2">
            <label class="small" for="start_Date">
              <?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
            </label>       
            <input type="text" name="start_date" id="start_date" size="30" value="<?php echo $start_date; ?>" class="start_date span9" autocomplete="Off" />
          </div>
          <div class="span2">
            <label class="small" for="end_date">
              <?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
            </label>   
            <input type="text" name="end_date" id="end_date" size="30" value="<?php echo $end_date; ?>" class="end_date span9" autocomplete="Off"/>
          </div>    
          <div class="span1">
            <label class="small" for="search_sleeps">
              <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
            </label> 
            <select id="search_sleeps" class="span12" name="occupancy">
              <?php echo JHtml::_('select.options', array(0 => '...', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $occupancy); ?>
            </select>
          </div>
          <div class="span1">
            <label class="small" for="search_bedrooms">
              <?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
            </label>
            <select id="search_bedrooms" class="span12" name="bedrooms">
              <?php echo JHtml::_('select.options', array(0 => '...', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $bedrooms); ?>
            </select>
          </div>

          <div class="span2">
            <button id="property-search-button" class="btn btn-large btn-primary pull-right" href="#" style="margin-top:18px;">
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
          <h2><?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_HEADING'); ?></h2>
          <p><?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_BODY'); ?></p>
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
          <div class="span-12">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#list" data-toggle="tab">List</a></li>
              <li><a href="#mapsearch" data-toggle="tab">Map</a></li>
              <li><a href="#localinfo" data-toggle="tab">Info</a></li>
              <li class="pull-right form-inline">
                <label for="sort_by" class="control-label small">
                  <?php echo JText::_('COM_FCSEARCH_SEARCH_SORT_BY'); ?>
                </label>
                <select id="search_bedrooms" class="small input-small" name="sort_by">
                  <?php echo JHtml::_('select.options', array('' => JText::_('COM_FCSEARCH_SEARCH_PLEASE_CHOOSE')), 'value', 'text', $bedrooms); ?>
                </select>

            </ul>
          </div>
        </div>
        <div class="tab-content">
          <div class="tab-pane active" id="list">
            <div class="row-fluid">
              <div class="span9">
                <div class="search-pagination">
                  <div class="pagination small">
                    <?php echo $this->pagination->getPagesLinks(); ?>
                    <p class="small pull-right" style="line-height:34px;"><?php echo $this->pagination->getResultsCounter(); ?></p>
                  </div>        
                  <ul class="search-results list-striped">
                    <?php
                    for ($i = 0, $n = count($this->results); $i < $n; $i++) {
                      $this->result = &$this->results[$i];
                      if (!empty($this->result->id)) {
                        echo $this->loadTemplate('result');
                      }
                    }
                    ?>
                  </ul>
                </div>
                <div class="search-pagination">
                  <div class="pagination">
                    <?php echo $this->pagination->getPagesLinks(); ?>
                  </div>
                </div>
              </div>
              <div class="span3">
                <?php echo $this->loadTemplate('refine'); ?>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="mapsearch">
            <div class="row-fluid">
              <div class="span9">
                <div id="map_canvas"></div>
              </div>
              <div class="span3">
                <?php echo $this->loadTemplate('refine'); ?>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="localinfo">
            <div class="row-fluid">
              <div class="span9">
                <p>Localinfo, innit!</p>
              </div>
              <div class="span3">
                <p>Other</p>
              </div>
            </div>
          </div>
        </div>
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
      <?php endif; ?>


      <input type="hidden" name="option" value="com_fcsearch" />

    </form>
  </div>

</div>
