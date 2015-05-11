<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
$route = JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->result->id . '&unit_id=' . (int) $this->result->unit_id);

$user = JFactory::getUser();
$lang = JFactory::getLanguage();
$lang->load('com_accommodation', JPATH_SITE);
$lang->load('com_fcsearch', JPATH_SITE);

$logged_in = ($user->guest) ? false : true;
?>

<div class="search-result">
  <div class="row">
    <div class="col-xs-12 col-sm-9">     
      <p class="">
        <a title="<?php echo JText::sprintf('COM_SHORTLIST_REMOVE_FROM_LIST',$this->escape($this->result->unit_title)); ?>" class="btn btn-danger btn-xs" data-id='<?php echo $this->result->id ?>' href="<?php echo JRoute::_('index.php?option=com_shortlist&task=shortlist.remove&id=' . (int) $this->result->unit_id . '&action=remove') ?>">
          <i class="glyphicon glyphicon-remove small"></i>
        </a>
      </p>
      <h3 class="listing-title">
        <a href="<?php echo JRoute::_($route); ?>"><?php echo $this->escape(trim($this->result->unit_title)); ?></a>
        <small>
          <?php echo $this->result->property_type . ', ' . $this->result->location_title ?>
        </small>
      </h3>
    </div>
    <div class="col-xs-12 col-sm-3">
      <p class="rates">
        <?php if ($this->result->price) : ?>
          <?php echo JText::_('COM_FCSEARCH_SEARCH_FROM'); ?>
          <span class="lead">
            <?php echo '&pound;' . round($this->result->price); ?>
          </span>
          <span class="rate-per">
            <?php echo $this->result->tariff_based_on; ?>
          </span>
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
          <?php if (!empty($this->result->thumbnail)) : ?>
            <img class="img-responsive" src='/images/property/<?php echo $this->result->unit_id . '/thumb/' . $this->result->thumbnail ?>' />
          <?php else: ?>
            <img width="210" class="img-responsive" src="<?php echo $uri->getScheme() . '://' . $this->result->url_thumb ?>" />
          <?php endif; ?>
        </a>  
      </p>
    </div>
    <div class="col-xs-12 col-sm-9">
      <div class="row">
        <div class="col-md-9 col-sm-9">
          <p>
            <?php
            echo JText::sprintf('COM_FCSEARCH_SITE_OCCUPANCY_DETAIL', $this->result->accommodation_type, $this->result->property_type, $this->result->bedrooms, $this->result->bathrooms, $this->result->occupancy);
            echo ($this->result->changeover_day) ? '&nbsp;' . JText::sprintf('COM_FCSEARCH_CHANGEOVER_DAY', $this->result->changeover_day) : '';
            echo (!empty($this->result->distance)) ? JText::sprintf('COM_FCSEARCH_SITE_DISTANCE', (float) $this->result->distance, $this->escape($location)) : '';
            ?>
          </p>
          <p>
            <?php echo JHtml::_('string.truncate', $this->result->description, 100, true, false); ?>
          </p>
          <p class=""><?php echo JText::sprintf('COM_FCSEARCH_REF', $this->result->id); ?></p>
        </div>
        <div class="col-md-3 col-sm-3">  
          <p class="view-property-button visible-xs-inline-block visible-sm-block visible-md-block visible-lg-block">
            <a href="<?php echo $route ?>" class="btn btn-primary">
              <?php echo ($this->result->is_bookable) ? JText::_('COM_FCSEARCH_BOOK_NOW') : JText::_('COM_FCSEARCH_VIEW_PROPERTY') ?>
            </a>
          </p>

          <?php if ($this->result->reviews) : ?>
            <p class="listing-reviews visible-xs-inline-block visible-xs-inline-block visible-sm-block visible-md-block visible-lg-block">
              <a href="<?php echo $route . '#reviews' ?>">
                <?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_HAS_NUMBER_OF_REVIEWS', $this->result->reviews); ?>
              </a>
            </p>
          <?php endif; ?> 
        </div>
      </div>
    </div>
  </div>
</div>