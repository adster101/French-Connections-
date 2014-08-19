<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if (!empty($displayData))
{

  $input = JFactory::getApplication()->input;
  $preview = $input->get('preview', '', 'int');

  $link = 'index.php?option=com_accommodation&Itemid=' . (int) $displayData->itemid . '&id=' . (int) $displayData->property_id . '&unit_id=' . (int) $displayData->unit_id;

  if ((int) $preview && $preview == 1)
  {
    $link .= '&preview=1';
  }

  $route = JRoute::_($link);
}
?>
<div class="navbar navbar-default navbar-property-navigator hidden-xs" data-spy="affix" data-offset-top="160" >
  <div class="container">
    <ul class="nav navbar-nav">
      <li>
        <a href="<?php echo $route ?>#top">
          <i class="icon icon-home"> </i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TOP'); ?>
        </a>
      </li>
      <li>
        <a href="<?php echo $route ?>#about"><?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_DESCRIPTION'); ?></a>
      </li>
      <?php if (!empty($displayData->location_details)) : ?>
        <li>
          <a href="<?php echo $route ?>#location">
            <i class="icon icon-location"></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_LOCATION'); ?>
          </a>
        </li>
      <?php endif; ?>
      <?php if (!empty($displayData->getting_there)) : ?>
        <li>
          <a href="<?php echo $route ?>#gettingthere">
            <i class="icon icon-compass"></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TRAVEL'); ?>
          </a>
        </li>
      <?php endif; ?>
      <?php if ($displayData->reviews && count($displayData->reviews) > 0) : ?>
        <li>
          <a href="<?php echo $route ?>#reviews">
            <i class="icon icon-star "></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_REVIEWS'); ?>
          </a>
        </li>
      <?php endif; ?>
      <li>
        <a href="<?php echo $route ?>#facilities">
          <i class="icon icon-power-cord"></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_FACILITIES'); ?>
        </a>
      </li>
      <li>
        <a href="<?php echo $route ?>#availability">
          <i class="icon icon-calendar-2"></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_AVAILABILITY'); ?>
        </a>
      </li>
      <li>
        <a href="<?php echo $route ?>#tariffs">
          <i class="icon icon-credit"></i><?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TARIFFS'); ?>
        </a>
      </li>
      <li>
        <a href="<?php echo $route ?>#email">
          <i class="icon icon-envelope"></i>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_CONTACT'); ?>
        </a>
      </li>
    </ul>
  </div>
</div>