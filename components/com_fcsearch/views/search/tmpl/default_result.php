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
$action = (array_key_exists($this->result->unit_id, $this->shortlist)) ? 'remove' : 'add';
$Itemid_property = SearchHelper::getItemid(array('component', 'com_accommodation'));
$HolidayMakerLogin = SearchHelper::getItemid(array('component', 'com_users'));
$Shortlist_ItemID = SearchHelper::getItemid(array('component', 'com_shortlist'));
$shortlist_route = JRoute::_('index.php?Itemid=' . (int) $Shortlist_ItemID);
$login_route = JRoute::_('index.php?option=com_users&Itemid=' . (int) $HolidayMakerLogin . '&return=' . base64_encode($shortlist_route));
$route = JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid_property . '&id=' . (int) $this->result->id . '&unit_id=' . (int) $this->result->unit_id);
$location = UCFirst(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));
$inShortlist = (array_key_exists($this->result->unit_id, $this->shortlist)) ? 1 : 0;
$shortlist = new JLayoutFile('frenchconnections.general.shortlist');
$displayData = new StdClass;
$displayData->action = $action;
$displayData->inShortlist = $inShortlist;
$displayData->unit_id = $this->result->unit_id;
$displayData->class = '';
$description = JHTml::_('string.truncate', $this->result->description, 50, true, false);
$title = JText::sprintf('COM_FCSEARCH_THUMBNAIL_TITLE', $this->result->id, $description);
$uri = JUri::getInstance();
$from_price = JHtmlGeneral::price($this->result->price, $this->result->base_currency);

?>

<div class="search-result">
  <div class="row">
    <div class="col-xs-12 col-sm-9">
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
              <?php //echo '&pound;' . $price['GBP'] ?>
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
            echo (!empty($this->result->coast)) ? JText::sprintf('COM_FCSEARCH_SITE_DISTANCE_TO_COAST', (float) $this->result->coast) : '';
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
          <p class="shortlist-button visible-xs-inline-block visible-xs-inline-block visible-sm-block visible-md-block visible-lg-block">
            <?php if ($logged_in) : ?>
                <?php echo $shortlist->render($displayData); ?>

            <?php else : ?>
                <a class="lead" href="<?php echo JRoute::_($login_route); ?>" title="<?php echo JText::_('COM_FCSEARCH_LOGIN_TO_MANAGE_SHORTLIST') ?>">
                  <i class="glyphicon glyphicon-heart"></i>
                </a>    
            <?php endif; ?>
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