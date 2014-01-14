<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
$pathway = explode('/', $this->result->path);
$route = JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->result->id . '&unit_id=' . (int) $this->result->unit_id);
?>

<li>
</p>
<p class="pull-right">
  <a class="btn btn-small" href="#">
    <i class="icon-bookmark"> </i><?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_TO_FAVOURITES') ?>
  </a>
</p>
<h3>
  <a href="<?php echo JRoute::_($route); ?>"><?php echo $this->result->unit_title; ?></a>
  <small><?php echo $this->result->property_type . ', ' . $this->result->location_title ?></small>
</h3>



<div class="row-fluid">
  <div class="span4">
    <a href="<?php echo $route ?>" class="thumbnail pull-left">
      <img src='/images/property/<?php echo $this->result->unit_id . '/thumb/' . $this->result->thumbnail ?>' class="img-rounded" />
    </a>
  </div>
  <div class="span6">

    <p class="small">
      <?php echo JHtml::_('string.truncate', $this->result->description, 175, true, true); ?>
    </p>

    <hr class="condensed" />
    <span class="small">
      <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_OCCUPANCY_DETAIL', $this->result->bedrooms, $this->result->accommodation_type, $this->result->property_type, $this->result->occupancy); ?>
    </span>
  </div>
  <div class="span2" style="text-align:right;">
    <p class="">
      <?php if ($this->result->price) : ?>
        <?php echo JText::_('COM_FCSEARCH_SEARCH_FROM'); ?>
        <span class="lead">
          <?php echo '&pound;' . round($this->result->price); ?>
        </span>
        <br />
        <span class="small"><?php echo $this->result->tariff_based_on; ?><span>

          <?php else : ?>
            <?php echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST'); ?>
          <?php endif; ?>
          </p>

          <?php if ($this->result->reviews) : ?>
            <p class="small">
              <?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_HAS_NUMBER_OF_REVIEWS', $this->result->reviews); ?>
            </p>
          <?php endif; ?>


          <a href="<?php echo JRoute::_('index.php?option=com_accommodation&view=listing&id=' . $this->result->id) ?>" class="btn  btn-primary pull-right">
            <?php echo JText::_('VIEW') ?>
          </a>
          </div>
          </div>
          </li>
