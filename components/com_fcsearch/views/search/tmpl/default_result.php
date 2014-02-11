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

$logged_in = ($user->guest) ? false : true;
$action = (array_key_exists($this->result->unit_id, $this->shortlist)) ? 'remove' : 'add';
?>

<li>
  <div class="results-header clearfix">
    <p class="pull-right" style="text-align:right">
      <?php if ($this->result->price) : ?>
        <?php echo JText::_('COM_FCSEARCH_SEARCH_FROM'); ?>
        <span class="lead">
          <?php echo '&pound;' . round($this->result->price); ?>
        </span>
        <br />
        <span class="small">
          <?php echo $this->result->tariff_based_on; ?>
        </span>
      <?php else : ?>
        <?php echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST'); ?>
      <?php endif; ?>
    </p> 
    <h3>
      <a href="<?php echo JRoute::_($route); ?>"><?php echo $this->escape(trim($this->result->unit_title)); ?></a>
      <small>
        <?php echo $this->result->property_type . ', ' . $this->result->location_title ?>
      </small>
    </h3>
  </div>
  <div class="row-fluid">
    <div class="span10 clearfix">
      <a href="<?php echo $route ?>" class="thumbnail pull-left">
        <img src='/images/property/<?php echo $this->result->unit_id . '/thumb/' . $this->result->thumbnail ?>' />
      </a>
      <p>
        <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_OCCUPANCY_DETAIL', $this->result->bedrooms, $this->result->accommodation_type, $this->result->property_type, $this->result->occupancy); ?>
      <hr />
      <?php echo JHtml::_('string.truncate', $this->result->description, 175, true, false); ?>
      </p>
    </div>
    <div class="span2 align-right result-links">
      <p>
        <a href="<?php echo JRoute::_('index.php?option=com_accommodation&view=listing&id=' . $this->result->id) ?>" class="btn btn-primary">
          <?php echo JText::_('VIEW') ?>
        </a>
      </p>
      <p>
        <?php if ($logged_in) : ?>
          <a class="shortlist lead <?php echo ($action == 'add') ? 'muted' : '' ?>" data-animation="false" data-placement="left" data-toggle="popover" data-id='<?php echo $this->result->unit_id ?>' data-action='<?php echo $action ?>' href="#">
            <i class="icon icon-heart"></i>
          </a>
        <?php else : ?>
          <a class="login lead" href="#" data-return="<?php echo base64_encode('/shortlist'); ?>" data-toggle="tooltip" title="<?php echo JText::_('COM_FCSEARCH_LOGIN_TO_MANAGE_SHORTLIST') ?>">
            <i class="icon icon-heart muted"></i>
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