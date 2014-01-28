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
?>
<div class="finder">
  <h1>
    <small><?php echo $this->escape(str_replace(' - French Connections', '', $this->document->title)); ?></small>
  </h1>
  <div id="search-form">
    <form id="property-search" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=en&Itemid=165&s_kwds=' . $s_kwds) ?>" method="POST" class="form-vertical">
      <?php
      if (0):
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
                <select id="sort_by" class="small input-medium" name="order">
                  <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $ordering); ?>
                </select>
            </ul>
          </div>
        </div>
        <div class="row-fluid">
          <div class="tab-content span9">
            <div class="tab-pane active" id="list">
              <div class="row-fluid">
                <div class="span9">
                  <div class="search-pagination hidden-phone">
                    <div class="pagination small ">
                      <?php echo $this->pagination->getPagesLinks(); ?>
                    </div>
                  </div>
                </div>
                <div class="span3">
                  <p class="" style="line-height:34px;"><?php echo $this->pagination->getResultsCounter(); ?></p>
                </div>
              </div>
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
      
      <?php endif; ?>


      <input type="hidden" name="option" value="com_fcsearch" />
      <?php
      // Following method adds a hidden field which essentially tracks the state of the search
      // Possibly, this could/would be better in session scope?
      echo $this->getFilters();
      ?>
      <?php echo JHtml::_('form.token'); ?>
    </form>
  </div>

</div>
 
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

