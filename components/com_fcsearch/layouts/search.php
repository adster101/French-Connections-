<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$displayData = ($displayData) ? $displayData : new stdClass;
$searchterm = $displayData->searchterm;
$bedrooms = $displayData->bedrooms;
$occupancy = $displayData->occupancy;
$arrival = $displayData->arrival;
$departure = $displayData->departure;
$lastminute = (!empty($displayData->lastminute)) ? $displayData->lastminute : false;
$colour = (!empty($displayData->colour)) ? $displayData->colour : 'primary';
?>
<?php echo JHtml::_('form.token'); ?>
<div class="form-group">

  <label class="sr-only" for="q">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
  </label>
  <input id="s_kwds" class="typeahead search-box form-control" type="text" name="s_kwds" autocomplete="Off" size="40"
         value="<?php echo $searchterm ?>" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_DESTINATION_OR_PROPERTY') ?>" />
</div>
<div class="form-group">
  <label class="sr-only" for="occupancy">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
  </label>
  <select id="occupancy" name="occupancy" class="form-control">
    <?php echo JHtml::_('select.options', array('' => JText::_('COM_FCSEARCH_ACCOMMODATION_PEOPLE'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18, 19 => 19, 20 => 20, 21 => 21, 22 => 22, 23 => 23, 24 => 24, 25 => 25, 26 => 26, 27 => 27, 28 => 28, 29 => 29, 30 => 30), 'value', 'text', $occupancy); ?>
  </select>

</div>
<div class="form-group">
  <label class="sr-only" for="bedrooms">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
  </label>
  <select id="bedrooms" name="bedrooms" class="form-control" >
    <?php echo JHtml::_('select.options', array('' => JText::_('COM_FCSEARCH_ACCOMMODATION_BEDROOMS'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => '10+'), 'value', 'text', $bedrooms); ?>
  </select>
</div>
<div class="form-group">
  <label class="sr-only" for="arrival">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
  </label>
  <div class="input-group start_date date">
    <input type="text" name="arrival" id="arrival" value="<?php echo $arrival ?>" size="10"
           class="form-control" autocomplete="Off" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_ARRIVAL_DATE') ?>" />
    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar" for="arrival"></i></span>
  </div>
</div>
<div class="form-group">
  <label class="sr-only" for="departure">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
  </label>
  <div class="input-group end_date date">
    <input type="text" name="departure" aria-labelled-by="cal" id="departure" size="10" value="<?php echo $departure ?>" 
           class="form-control" autocomplete="off" placeholder="<?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_DEPARTURE_DATE') ?>" />
    <span class="input-group-addon"><i id="cal" class="glyphicon glyphicon-calendar"></i></span>
  </div>
</div>
<?php if ($lastminute) : ?>
  <ul class="unstyled">
    <?php for ($i = 0; $i < 6; $i++) : ?>
      <?php $days = $i * 7; // Determine number of days to add to the next changeover day to get the nth week (assuming 6 weeks) ?>
      <?php $days_plus_week = ($i * 7) + 7; // As above, only adding an additional seven days to get departure date ?>
      <?php $arrival_date = new DateTime(date("d-m-Y", strtotime('next Saturday +' . $days . ' day'))); // The next available sat changeover day?>
      <?php $end_date = new DateTime(date("d-m-Y", strtotime('next Saturday +' . $days_plus_week . ' day'))); // The next available sat departure?>
      <li>
        <a href="#" data-start="<?php echo $arrival_date->format('d-m-Y'); ?>" data-end="<?php echo $end_date->format('d-m-Y'); ?>" class="lastminute-date-search-link">
          <?php echo $arrival_date->format('D j M Y'); ?>
          <?php echo $end_date->format('D j M Y'); ?>
        </a>
      </li>
    <?php endfor; ?>
  </ul>
<?php endif; ?>
<button class="property-search-button btn btn-<?php echo $colour; ?>">
  <i class="icon-search icon-white"> </i>
  <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
</button>
<input type="hidden" name="option" value="com_fcsearch" />


