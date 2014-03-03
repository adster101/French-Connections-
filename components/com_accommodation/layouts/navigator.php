<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if (!empty($displayData)) {

  $input = JFactory::getApplication()->input;
  $preview = $input->get('preview', '', 'int');

  $link = 'index.php?option=com_accommodation&Itemid=' . (int) $displayData->itemid . '&id=' . (int) $displayData->property_id . '&unit_id=' . (int) $displayData->unit_id;

  if ((int) $preview && $preview == 1) {
    $link .= '&preview=1';
  }

  $route = JRoute::_($link);
}
?>
<ul class="nav nav-pills navigator">
  <li>
    <a href="<?php echo $route ?>#top">
      <i class="icon icon-home"> </i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TOP'); ?>
    </a>
  </li>
  <li <?php echo ($displayData->navigator == 'about') ? 'class="active"' : '' ?>>
    <a href="<?php echo $route ?>#about"><?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_DESCRIPTION'); ?></a>
  </li>
  <?php if (!empty($displayData->location)) : ?>
    <li <?php echo ($displayData->navigator == 'location') ? 'class="active"' : '' ?>>
      <a href="<?php echo $route ?>#location">
        <i class="icon icon-location"></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_LOCATION'); ?>
      </a>
    </li>
  <?php endif; ?>
  <?php if (!empty($displayData->gettingthere)) : ?>
    <li <?php echo ($displayData->navigator == 'gettingthere') ? 'class="active"' : '' ?>>
      <a href="<?php echo $route ?>#gettingthere">
        <i class="icon icon-compass"></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TRAVEL'); ?>
      </a>
    </li>
  <?php endif; ?>
  <?php if ($displayData->reviews && count($displayData->reviews) > 0) : ?>
    <li <?php echo ($displayData->navigator == 'reviews') ? 'class="active"' : '' ?>>
      <a href="<?php echo $route ?>#reviews">
        <i class="icon icon-star "></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_REVIEWS'); ?>
      </a>
    </li>
  <?php endif; ?>
  <li <?php echo ($displayData->navigator == 'facilities') ? 'class="active"' : '' ?>>
    <a href="<?php echo $route ?>#facilities">
      <i class="icon icon-power-cord"></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_FACILITIES'); ?>
    </a>
  </li>
  <li <?php echo ($displayData->navigator == 'availability') ? 'class="active"' : '' ?>>
    <a href="<?php echo $route ?>#availability">
      <i class="icon icon-calendar-2"></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_AVAILABILITY'); ?>
    </a>
  </li>
  <li <?php echo ($displayData->navigator == 'tariffs') ? 'class="active"' : '' ?>>
    <a href="<?php echo $route ?>#tariffs">
      <i class="icon icon-credit"></i><?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TARIFFS'); ?>
    </a>
  </li>
  <li <?php echo ($displayData->navigator == 'email') ? 'class="active"' : '' ?>>
    <a href="<?php echo $route ?>#email">
      <i class="icon icon-envelope"></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_CONTACT'); ?>
    </a>
  </li>
</ul>
