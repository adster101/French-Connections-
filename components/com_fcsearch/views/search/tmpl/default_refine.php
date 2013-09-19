<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();


$uri = str_replace('http://', '', JUri::current());

$refine_budget_min = $this->getBudgetFields();
$refine_budget_max = $this->getBudgetFields(250, 5000, 250, 'max_');

$min_budget = $app->input->request->get('min');
$max_budget = $app->input->request->get('max');
$hide = '';
$searchterm = UCFirst(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));
$bedrooms = $this->state->get('list.bedrooms');
$occupancy = $this->state->get('list.occupancy');
$arrival = $this->state->get('list.arrival');
$departure = $this->state->get('list.departure');
?>


<div class="well well-small">
  <label class="small" for="q">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
  </label>
  <input id="s_kwds" class="typeahead span12" type="text" name="s_kwds" autocomplete="Off" value="<?php echo $searchterm ?>"/>
  <div class="row-fluid">
    <div class="span6">
      <label class="small" for="arrival">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
      </label>
      <input type="text" name="arrival" id="arrival" size="30" value="<?php echo $arrival; ?>" class="input-mini start_date" autocomplete="Off" />
    </div>
    <div class="span6">
      <label class="small" for="departure">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
      </label>
      <input type="text" name="departure" id="departure" size="30" value="<?php echo $departure; ?>" class="end_date input-mini " autocomplete="Off"/>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span6">
      <label class="small" for="occupancy">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
      </label>
      <select id="occupancy" class="span12" name="occupancy">
        <?php echo JHtml::_('select.options', array('' => '...', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $occupancy); ?>
      </select>
    </div>
    <div class="span6">
      <label class="small" for="bedrooms">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
      </label>
      <select id="bedrooms" class="span12" name="bedrooms">
        <?php echo JHtml::_('select.options', array('' => '...', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 'value', 'text', $bedrooms); ?>
      </select>
    </div>
  </div>
  <button id="property-search-button" class="btn btn-large btn-primary pull-right" href="#" style="margin-top:18px;">
    <i class="icon-search icon-white"> </i>
    <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
  </button>
</div>
<div class="refine">

  <h4><?php echo JText::_('COM_FCSEARCH_SEARCH_REFINE_SEARCH'); ?></h4>

  <div class="accordion" id="accordion2">
    <div class="accordion-group">
      <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" href="#budget">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_REFINE_BUDGET'); ?>
        </a>
      </div>
      <div id="budget" class="accordion-body collapse in">
        <div class="accordion-inner">
          <div class="row-fluid">
            <div class="span6">
              <label for="min_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MINIMUM_PRICE_RANGE'); ?></label>
              <select id="min_price" name="min" class="span12">
                <?php echo JHtml::_('select.options', $refine_budget_min, 'value', 'text', $min_budget); ?>
              </select>
            </div>
            <div class="span6">
              <label for="max_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MAXIMUM_PRICE_RANGE'); ?></label>
              <select id="max_price" name="max" class="span12">
                <?php echo JHtml::_('select.options', $refine_budget_max, 'value', 'text', $max_budget); ?>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php foreach ($this->refine_options as $key => $values) : ?>
      <?php
      $counter = 0;
      $hide = true // Init a counter so we don't show all the options at once
      ?>
      <div class="accordion-group">
        <div class="accordion-heading">
          <a class="accordion-toggle" data-toggle="collapse" href="#<?php echo $app->stringURLSafe($key) ?>">
            <?php echo $key; ?>
          </a>
        </div>
        <div id="<?php echo $app->stringURLSafe($key) ?>" class="accordion-body collapse">
          <div class="accordion-inner">
            <?php
            // Below should be abstracted into a helper function
            foreach ($values as $filter => $value) :

              $tmp = array_flip(explode('/', $uri));
              $remove = false;

              $filter_string = $value['search_code'] . JStringNormalise::toUnderscoreSeparated(JApplication::stringURLSafe($filter)) . '_' . $value['id'];

              if (array_key_exists($filter_string, $tmp)) {
                unset($tmp[$filter_string]);
                $new_uri = implode('/', array_flip($tmp));
                $remove = true;
              } else {
                $new_uri = implode('/', array_flip($tmp));
                $new_uri = $new_uri . '/' . $filter_string;
              }
              ?>
              <?php if ($counter >= 7 && $hide) : ?>
                <?php $hide = false; ?>
                <div class="hide ">
                <?php endif; ?>
                <p>
                  <a class="muted" href="<?php echo JRoute::_('http://' . $new_uri) ?>">
                    <i class="<?php echo ($remove ? 'icon-delete' : 'icon-new'); ?>"> </i>&nbsp;<?php echo $filter; ?> (<?php echo $value['count']; ?>)
                  </a>
                </p>
                <?php $counter++; ?>
                <?php if ($counter == count($values) && !$hide) : ?>

                </div>
              <?php endif; ?>
              <?php if ($counter == count($values) && !$hide) : ?>
                <hr class="condensed" />

                <a href="#" class="show" title="<?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS') ?>"><?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS'); ?></a>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

