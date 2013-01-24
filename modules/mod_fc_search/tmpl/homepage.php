<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$app = JFactory::getApplication();

$bedrooms = $app->getUserState('list.bedrooms');
$occupancy = $app->getUserState('list.occupancy');
$start_date = $app->getUserState('list.start_date');
$end_date = $app->getUserState('list.end_date');
?>
<form id="property-search" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=en&Itemid=165') ?>" method="GET" class="form-vertical">
  <div class="well clearfix">
    <h4 class="bottom"><?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_SEARCH') ?></h4>

    <label for="s_kwds">
      <?php echo JText::_('COM_FCSEARCH_SEARCH_DESTINATION') ?>
    </label>
    <input id="s_kwds" class="input-medium typeahead" type="text" name="q" autocomplete="Off" value=""/> 
    <div class="row-fluid">
      <div class="span6">
        <label for="start_date">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
        </label>
        <input id="start_date" class="span9 start_date" type="text" name="start_date" autocomplete="Off" value="<?php echo $start_date; ?>"/>    
      </div>
      <div class="span6">
        <label for="end_date">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
        </label>
        <input id="end_date" class="span9 end_date" type="text" name="end_date" autocomplete="Off" value="<?php echo $end_date; ?>" />    
      </div>
    </div>
    <div class="row-fluid">

      <div class="span6">
        <label for="search_sleeps">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
        </label>
        <select id="search_sleeps" class="input-mini" name="occupancy">
          <?php echo JHtml::_('select.options', array(0 => '...', 1 => 1, 2 => 2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'value', 'text', $occupancy); ?>          
        </select>
      </div>
      <div class="span6">
        <label for="search_bedrooms">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
        </label>
        <select id="search_bedrooms" class="input-mini" name="bedrooms">
          <?php echo JHtml::_('select.options', array(0 => '...', 1 => 1, 2 => 2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, '10+'=>10), 'value', 'text', $bedrooms); ?>
        </select>
      </div>
    </div>
    <button id="property-search-button" class="btn btn-large btn-primary pull-right" href="#">
      <i class="icon-search icon-white"> </i>
      <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
    </button>
  </div>
  <input type="hidden" name="option" value="com_fcsearch" />
</form>
<!-- Modal -->
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

