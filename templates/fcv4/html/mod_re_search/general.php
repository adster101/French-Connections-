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


$Itemid = SearchHelper::getItemid(array('component', 'com_realestatesearch'));

// The layout for the anchor based navigation on the property listing
$bedrooms = '';
$refine_budget_min = modReSearchHelper::getBudgetFields(25000, 1500000, 50000, 'min_');
$refine_budget_max = modReSearchHelper::getBudgetFields(25000, 1500000, 50000, 'max_','COM_FCSEARCH_SEARCH_MAXIMUM_PRICE');
?>
<div class="well well-light-blue">  
  <h4><?php echo JText::_('COM_REALESTATESEARCH_PROPERTY_SEARCH') ?></h4>
  <form class="form-inline" id="property-search" method="POST" action="<?php echo JRoute::_('index.php?option=com_realestatesearch&lang=' . $lang . '&Itemid=' . (int) $Itemid . '&s_kwds=' . JText::_('COM_FCSEARCH_S_KWDS_DEFAULT')) ?>">
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
             value="" 
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
        <?php echo JHtml::_('select.options', $refine_budget_min, 'value', 'text', ''); ?>
      </select>
    </div>
    <div class="form-group">
      <label class="sr-only" for="max_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MAXIMUM_PRICE_RANGE'); ?></label>
      <select id="max_price" name="max" class="form-control">
        <?php echo JHtml::_('select.options', $refine_budget_max, 'value', 'text', ''); ?>
      </select>
    </div>

    <button class="property-search-button btn btn-primary" href="#">
      <i class="icon-search icon-white"> </i>
      <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
    </button>
    <input type="hidden" name="option" value="com_realestatesearch" />
</div>
</form>
</div>
