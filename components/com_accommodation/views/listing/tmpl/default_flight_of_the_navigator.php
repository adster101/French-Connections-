<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
$logged_in = ($user->guest) ? false : true;

$inShortlist = (array_key_exists($this->item->unit_id, $this->shortlist)) ? 1 : 0;

$HolidayMakerLogin = SearchHelper::getItemid(array('component', 'com_users'));
$action = (array_key_exists($this->item->unit_id, $this->shortlist)) ? 'remove' : 'add';

// Shortlist button thingy
$shortlist = new JLayoutFile('frenchconnections.general.shortlist');
$displayData = new StdClass;
$displayData->action = $action;
$displayData->inShortlist = $inShortlist;
$displayData->unit_id = $this->item->unit_id;
$displayData->class = ' btn btn-default';


?>

  <a class="btn btn-sm btn-default" href="<?php echo $this->route ?>#top">
    <span class="glyphicon glyphicon-info-sign"></span>
    <?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TOP'); ?>
  </a>
  <?php if (!empty($this->item->location_details)) : ?>
    <a class="btn btn-sm btn-default" href="<?php echo $this->route ?>#location">
      <span class="glyphicon glyphicon-map-marker"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_LOCATION'); ?>
    </a>
    <?php endif; ?>
  <a class="btn btn-sm btn-default" href="<?php echo $this->route ?>#reviews">
    <span class="glyphicon glyphicon-star"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_REVIEWS'); ?>
  </a>
  <a class="btn btn-sm btn-default" href="<?php echo $this->route ?>#availability">
    <span class="glyphicon glyphicon-calendar"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_AVAILABILITY'); ?>
  </a>
  <a class="btn btn-sm btn-default" href="<?php echo $this->route ?>#tariffs">
    <span class="glyphicon glyphicon-euro"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TARIFFS'); ?>
  </a>
  <a class="btn btn-sm btn-default" href="<?php echo $this->route ?>#facilities">
    <span class="glyphicon glyphicon-th-list"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_FACILITIES'); ?>
  </a>
  <a class="btn btn-sm btn-default" href="<?php echo $this->route ?>#contact">
    <?php $contact_anchor_label = ($this->item->is_bookable) ? 'COM_ACCOMMODATION_NAVIGATOR_BOOK_NOW' : 'COM_ACCOMMODATION_NAVIGATOR_CONTACT'; ?>
    <span class="glyphicon glyphicon-envelope"></span>&nbsp;<?php echo JText::_($contact_anchor_label); ?>
  </a>

    <?php if ($logged_in) : ?>
        <?php echo $shortlist->render($displayData); ?>
    <?php else : ?>
        <a class="btn btn-default btn-sm" href="<?php echo JRoute::_('index.php?option=com_users&Itemid=' . (int) $HolidayMakerLogin) ?>">
          <span class="glyphicon glyphicon-heart muted"></span>
          <span class=""><?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?></span>
        </a>
    <?php endif; ?>
