<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$lang = $app->input->get('lang', 'en');


$Itemid = SearchHelper::getItemid(array('component', 'com_fcsearch'));

// The layout for the anchor based navigation on the property listing
$searchterm = '';
$bedrooms = '';
$occupancy = '';
$arrival = '';
$departure = '';
?>
<div class="panel-home-page-search-container" style="position:relative">
  <div class="panel panel-home-page-search">  
    <div class="panel-body">
      <h4><?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_SEARCH') ?></h4>
      <form class="form-inline" id="property-search" method="POST" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=' . $lang . '&Itemid=' . (int) $Itemid . '&s_kwds=' . JText::_('COM_FCSEARCH_S_KWDS_DEFAULT')) ?>">
        <label class="sr-only" for="q">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
        </label>
        <input id="s_kwds" class="typeahead search-box form-control" type="text" name="s_kwds" autocomplete="Off" value="<?php echo $searchterm ?>" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_DESTINATION_OR_PROPERTY') ?>" />

        <div class="form-group">
          <label class="sr-only" for="arrival">
            <?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
          </label>
          <div class="input-group start_date date">
            <input type="text" name="arrival" id="arrival" size="30" value="<?php echo $arrival ?>" 
                   class="form-control search-control-date" autocomplete="Off" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_ARRIVAL_DATE') ?>" />
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
          </div>
        </div>
        <div class="form-group">
          <label class="sr-only" for="departure">
            <?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
          </label>    
          <span class="input-group end_date date">

            <input type="text" name="departure" id="departure" size="30" value="<?php echo $departure ?>" 
                   class="form-control search-control-date" autocomplete="off" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_DEPARTURE_DATE') ?>" />
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
          </span>
        </div>
        <div class="form-group">
          <label class="sr-only" for="occupancy">
            <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
          </label>

          <select id="occupancy" name="occupancy" class="form-control search-control-occupancy">
            <?php echo JHtml::_('select.options', array('' => JText::_('COM_FCSEARCH_ACCOMMODATION_PEOPLE'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $occupancy); ?>
          </select>
        </div>
        
            <button class="property-search-button btn btn-primary">
              <i class="icon-search icon-white"> </i>
              <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
            </button>
     
        <input type="hidden" name="option" value="com_fcsearch" />
      </form>
    </div>
  </div>
</div>