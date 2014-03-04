<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$displayData = ($displayData) ? $displayData : new stdClass;
$searchterm = $displayData->searchterm;
$bedrooms = $displayData->bedrooms;
$occupancy = $displayData->occupancy;
$arrival = $displayData->arrival;
$departure = $displayData->departure;
?>
<?php echo JHtml::_('form.token'); ?>

<label class=" offscreen" for="q">
  <?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
</label>
<input id="s_kwds" class="typeahead search-box input-xlarge" type="text" name="s_kwds" autocomplete="Off" 
       value="<?php echo $searchterm ?>" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_DESTINATION_OR_PROPERTY') ?>" />

<div class="search-field">
  <label class="offscreen" for="occupancy">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
  </label>
  <select id="occupancy" name="occupancy" class="span12">
    <?php echo JHtml::_('select.options', array('' => JText::_('COM_FCSEARCH_ACCOMMODATION_PEOPLE'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $occupancy); ?>
  </select>
</div>
<div class="search-field">
  <label class="offscreen" for="bedrooms">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
  </label>
  <select id="bedrooms" name="bedrooms" class="span12">
    <?php echo JHtml::_('select.options', array('' => JText::_('COM_FCSEARCH_ACCOMMODATION_BEDROOMS'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $bedrooms); ?>
  </select>
</div>
<div class="search-field">
  <label class="offscreen" for="arrival">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
  </label>
  <input type="text" name="arrival" id="arrival" size="30" value="<?php echo $arrival ?>" 
         class="start_date input-small" autocomplete="Off" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_ARRIVAL_DATE') ?>" />
</div>
<div class="search-field">
  <label class="offscreen" for="departure">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
  </label>
  <input type="text" name="departure" id="departure" size="30" value="<?php echo $departure ?>" 
         class="end_date input-small" autocomplete="off" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_DEPARTURE_DATE') ?>" />
</div>
<button class="property-search-button btn btn-primary pull-right" href="#">
  <i class="icon-search icon-white"> </i>
  <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
</button>
<input type="hidden" name="option" value="com_fcsearch" />


