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
$Itemid_property = FCSearchHelperRoute::getItemid(array('component', 'com_accommodation'));
$route = JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid_property . '&id=' . (int) $this->result->id . '&unit_id=' . (int) $this->result->unit_id);
$location = UCFirst(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));
?>

<li>
  <div class="row">
    <div class="col-xs-12 col-sm-9">
      <h3>
        <a href="<?php echo JRoute::_($route); ?>"><?php echo $this->escape(trim($this->result->unit_title)); ?></a>
        <small>
          <?php echo $this->result->property_type . ', ' . $this->result->location_title ?>
        </small>
      </h3>
    </div>
    <div class="col-xs-12 col-sm-3">
      <p class="">
        <?php if ($this->result->price) : ?>
          <?php echo JText::_('COM_FCSEARCH_SEARCH_FROM'); ?>
          <span class="lead">
            <?php echo '&pound;' . round($this->result->price); ?>
          </span><br />
          <span class="small">
            <?php echo $this->result->tariff_based_on; ?>
          </span>
        <?php else : ?>
          <?php echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST'); ?>
        <?php endif; ?>
      </p> 
    </div>
  </div>


  <div class="row">
    <div class="col-xs-12 col-sm-10">
      <div class="media">
        <a href="<?php echo $route ?>" class="pull-left">
          <img class="image-responsive" src='/images/property/<?php echo $this->result->unit_id . '/thumb/' . $this->result->thumbnail ?>' />
        </a>
        <div class="media-body">
          <p>
            <?php
            echo JText::sprintf('COM_FCSEARCH_SITE_OCCUPANCY_DETAIL', $this->result->accommodation_type, $this->result->property_type, $this->result->bedrooms, $this->result->bathrooms, $this->result->occupancy);
            echo ($this->result->changeover_day) ? '&nbsp;' . JText::sprintf('COM_FCSEARCH_CHANGEOVER_DAY', $this->result->changeover_day) : '';
            echo (!empty($this->result->distance)) ? JText::sprintf('COM_FCSEARCH_SITE_DISTANCE', (float) $this->result->distance, $this->escape($location)) : '';
            ?>
          <hr />
          <?php echo JHtml::_('string.truncate', $this->result->description, 150, true, false); ?>
          </p>
        </div>
      </div>


    </div>
    <div class="col-xs-12 col-sm-2">
      <p>
        <a href="<?php echo $route ?>" class="btn btn-primary">
          <?php echo JText::_('COM_FCSEARCH_VIEW_PROPERTY') ?>
        </a>
      </p>
      <p>
        <?php if ($logged_in) : ?>
          <a class="shortlist lead <?php echo ($action == 'add') ? 'muted' : '' ?>" data-animation="false" data-placement="left" data-toggle="popover" data-id='<?php echo $this->result->unit_id ?>' data-action='<?php echo $action ?>' href="#">
            <i class="icon icon-heart"></i>
          </a>
        <?php else : ?>
          <a class="login lead" href="#" data-return="<?php echo base64_encode('/shortlist'); ?>" data-toggle="tooltip" title="<?php echo JText::_('COM_FCSEARCH_LOGIN_TO_MANAGE_SHORTLIST') ?>">
            <i class="glyphicon glyphicon-heart text-muted"></i>
          </a>    
        <?php endif; ?>
      </p> 

      <?php if ($this->result->reviews) : ?>
        <p class="small">
          <a href="<?php echo $route . '#reviews' ?>">
            <?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_HAS_NUMBER_OF_REVIEWS', $this->result->reviews); ?>
          </a>
        </p>
      <?php endif; ?> 
    </div>
  </div>
</li>