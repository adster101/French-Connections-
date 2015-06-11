<?php
defined('_JEXEC') or die('Restricted access');
?>
<div class="navbar-property-navigator" data-spy="affix" data-offset-top="640">

  <div class="col-lg-10 col-md-9 col-sm-8 hidden-xs">
    <ul class="nav nav-pills">
      <li>
        <a href="<?php echo $route ?>#top">
          <span class="glyphicon glyphicon-home"> </span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TOP'); ?>
        </a>
      </li>
      <li>
        <a href="<?php echo $route ?>#about">
          <span class="glyphicon glyphicon-info-sign"> </span>          
          <?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_DESCRIPTION'); ?>
        </a>
      </li>
      <?php if (!empty($this->item->location_details)) : ?>
        <li>
          <a href="<?php echo $route ?>#location">
            <span class="glyphicon glyphicon-map-marker"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_LOCATION'); ?>
          </a>
        </li>
      <?php endif; ?>
      <?php if (!empty($this->item->getting_there)) : ?>
        <li>
          <a href="<?php echo $route ?>#gettingthere">
            <span class="glyphicon glyphicon-plane"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TRAVEL'); ?>
          </a>
        </li>
      <?php endif; ?>
      <?php if ($this->item->reviews && count($this->item->reviews) > 0) : ?>
        <li>
          <a href="<?php echo $route ?>#reviews">
            <span class="glyphicon glyphicon-power-cord "></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_REVIEWS'); ?>
          </a>
        </li>
      <?php endif; ?>
      <li>
        <a href="<?php echo $route ?>#facilities">
          <span class="glyphicon glyphicon-power-cord"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_FACILITIES'); ?>
        </a>
      </li>
      <li>
        <a href="<?php echo $route ?>#availability">
          <span class="glyphicon glyphicon-calendar"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_AVAILABILITY'); ?>
        </a>
      </li>
      <li>
        <a href="<?php echo $route ?>#tariffs">
          <span class="glyphicon glyphicon-credit-card"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TARIFFS'); ?>
        </a>
      </li>
      <li>
        <a href="<?php echo $route ?>#email">
          <?php $contact_anchor_label = ($this->item->is_bookable) ? 'COM_ACCOMMODATION_NAVIGATOR_BOOK_NOW' : 'COM_ACCOMMODATION_NAVIGATOR_CONTACT'; ?>
          <span class="glyphicon glyphicon-envelope"></span>&nbsp;<?php echo JText::_($contact_anchor_label); ?>
        </a>
      </li>
    </ul>
  </div>
</div>

