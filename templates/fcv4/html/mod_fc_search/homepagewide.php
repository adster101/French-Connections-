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
$lang = $app->input->get('lang', 'en');

$menus = $app->getMenu('site');

$Itemid = FCSearchHelperRoute::getItemid(array('component', 'com_fcsearch'));

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
      <form id="property-search" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=' . $lang . '&Itemid=' . (int) $Itemid . '&s_kwds=' . JText::_('COM_FCSEARCH_S_KWDS_DEFAULT')) ?>" method="POST" class="form-horizontal">
        <div class="form-group row">
          <div class="col-lg-12">
            <label class="sr-only" for="q">
              <?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
            </label>
            <input id="s_kwds" class="typeahead search-box form-control col-lg-12 col-md-12 col-sm-12 col-xs-12" type="text" name="s_kwds" autocomplete="Off" 
                   value="<?php echo $searchterm ?>" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_DESTINATION_OR_PROPERTY') ?>" />
          </div>
        </div>

        <div class="form-group row">
          <div class="col-xs-4 col-md-4 col-lg-4 col-sm-4">
            <label class="sr-only" for="arrival">
              <?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
            </label>
            <input type="text" name="arrival" id="arrival" size="30" value="<?php echo $arrival ?>" 
                   class="start_date form-control" autocomplete="Off" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_ARRIVAL_DATE') ?>" />
          </div>
          <div class="col-xs-4 col-md-4 col-lg-4 col-sm-4">
            <label class="sr-only" for="departure">
              <?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
            </label>
            <input type="text" name="departure" id="departure" size="30" value="<?php echo $departure ?>" 
                   class="end_date form-control" autocomplete="off" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_DEPARTURE_DATE') ?>" />
          </div>
          <div class="col-xs-4 col-md-4 col-lg-4 col-sm-4">
            <label class="sr-only" for="occupancy">
              <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
            </label>
            <select id="occupancy" name="occupancy" class="form-control">
              <?php echo JHtml::_('select.options', array('' => JText::_('COM_FCSEARCH_ACCOMMODATION_PEOPLE'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $occupancy); ?>
            </select>
          </div>
        </div>

        <button class="property-search-button btn btn-primary pull-right">
          <i class="icon-search icon-white"> </i>
          <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
        </button>
        <input type="hidden" name="option" value="com_fcsearch" />
      </form>
    </div>
  </div>
</div>