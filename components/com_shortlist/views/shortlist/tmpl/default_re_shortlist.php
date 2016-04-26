<?php
/**
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$user = JFactory::getUser();
$lang = JFactory::getLanguage();
$lang->load('com_realestate', JPATH_SITE);
$lang->load('com_realestatesearch', JPATH_SITE);
$Itemid_property = SearchHelper::getItemid(array('component', 'com_realestate'));
$route = JRoute::_('index.php?option=com_realestate&Itemid='.$Itemid_property.'&id='.(int) $this->result->property_id);
$prices = JHtml::_('general.price', $this->result->price, 'GBP', '', '');

?>

<div class="search-result">
<div class="row">
<div class="col-xs-12 col-sm-9">
       <p class="">
        <a title="<?php echo JText::sprintf('COM_SHORTLIST_REMOVE_FROM_LIST',$this->escape($this->result->unit_title)); ?>" class="btn btn-danger btn-xs" data-id='<?php echo $this->result->id ?>' href="<?php echo JRoute::_('index.php?option=com_shortlist&task=shortlist.remove&id=' . (int) $this->result->property_id . '&action=remove') ?>">
          <i class="glyphicon glyphicon-remove small"></i>
        </a>
      </p>
      <h3 class="listing-title">
        <a href="<?php echo JRoute::_($route); ?>"><?php echo $this->escape(trim($this->result->unit_title)); ?></a>
        <small>
          <?php echo $this->result->location_title ?>
        </small>
      </h3>
    </div>
    <div class="col-xs-12 col-sm-3">
      <p class="rates">
        <?php if ($this->result->price) : ?>
            <span class="lead">
              <?php echo '&pound;'.number_format(round($prices['GBP'])); ?>
            </span>
            (<?php echo '&euro;'.number_format(round($prices['EUR'])); ?>)

        <?php else : ?>
            <?php echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST'); ?>
        <?php endif; ?>
      </p>
    </div>
  </div>
  <?php if (!empty($this->result->offer)) : ?>
      <div class="row">
        <div class="col-xs-12">
          <p class="offer">
            <strong><span class="glyphicon glyphicon-tags"></span></strong>&nbsp;
            <?php echo $this->escape($this->result->offer); ?>
          </p>
        </div>
      </div>
  <?php endif; ?>
  <div class="row">
    <div class="col-xs-12 col-sm-3">
      <p>
        <a href="<?php echo $route ?>" title ="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
          <img class="img-responsive" src='/images/property/<?php echo $this->result->property_id.'/thumb/'.$this->result->thumbnail ?>' />
        </a>
      </p>
    </div>
    <div class="col-xs-12 col-sm-9">
      <div class="row">
        <div class="col-md-9 col-sm-9">
          <p>
            <?php if ($this->result->bedrooms && $this->result->bathrooms) : ?>
                <?php echo JText::sprintf('COM_REALESTATE_SEARCH_FACILITY_COUNT', $this->result->bedrooms, $this->result->bathrooms) ?>
            <?php elseif ($this->result->bedrooms) : ?>
                <?php echo JText::sprintf('COM_REALESTATE_SEARCH_BEDROOMS_COUNT', $this->result->bedrooms) ?>
            <?php elseif ($this->result->bathrooms) : ?>
                <?php echo JText::sprintf('COM_REALESTATE_SEARCH_BATHROOMS_COUNT', $this->result->bathrooms) ?>
            <?php endif; ?>
          </p>
          <p>
            <?php echo JHtml::_('string.truncate', $this->result->description, 150, true, false); ?>
          </p>
          <p class=""><?php echo JText::sprintf('COM_FCSEARCH_REF', $this->result->property_id); ?></p>
        </div>
        <div class="col-md-3 col-sm-3">
          <p class="view-property-button visible-xs-inline-block visible-sm-block visible-md-block visible-lg-block">
            <a href="<?php echo $route ?>" class="btn btn-primary">
              <?php echo JText::_('COM_FCSEARCH_VIEW_PROPERTY') ?>
            </a>
          </p>

        </div>
      </div>
    </div>
  </div>

</div>
