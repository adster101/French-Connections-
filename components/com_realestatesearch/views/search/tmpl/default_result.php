<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$user = JFactory::getUser();

$logged_in = ($user->guest) ? false : true;
$Itemid_property = SearchHelper::getItemid(array('component', 'com_realestate'));
$HolidayMakerLogin = SearchHelper::getItemid(array('component', 'com_users'));
$login_route = JRoute::_('index.php?option=com_users&Itemid=' . (int) $HolidayMakerLogin . '&return=' . base64_encode('/shortlist'));
$route = JRoute::_('index.php?option=com_realestate&Itemid=' . $Itemid_property . '&id=' . (int) $this->result->property_id);
$location = UCFirst(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));
$description = JHTml::_('string.truncate', $this->result->description, 50, true, false);
$title = JText::sprintf('COM_FCSEARCH_THUMBNAIL_TITLE', $this->result->id, $description);
$prices = JHtml::_('general.price', $this->result->price, $this->result->base_currency, '', '');
?>

<div class="search-result">
  <div class="row">
    <div class="col-xs-12 col-sm-9">
      <h3 class="listing-title">
        <a href="<?php echo JRoute::_($route); ?>"><?php echo $this->escape(trim($this->result->title)); ?></a>
        <small>
          <?php echo $this->result->location_title ?>
        </small>
      </h3>
    </div>
    <div class="col-xs-12 col-sm-3">
      <p class="rates">
        <?php if ($this->result->price) : ?>
          <span class="lead">
            <?php echo '&pound;' . number_format(round($prices['GBP'])); ?>
          </span>
          (<?php echo '&euro;' . number_format(round($prices['EUR'])); ?>)
          
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
          <img class="img-responsive" src='/images/property/<?php echo $this->result->property_id . '/thumb/' . $this->result->thumbnail ?>' />
        </a>  
      </p>
    </div>
    <div class="col-xs-12 col-sm-9">
      <div class="row">
        <div class="col-md-9 col-sm-9">
          <p>
            <?php echo JText::sprintf('COM_REALESTATE_SEARCH_FACILITY_COUNT', $this->result->bedrooms, $this->result->bathrooms) ?>
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