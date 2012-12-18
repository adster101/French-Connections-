<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<ul class="nav nav-tabs">
  <?php foreach ($this->units as $unit) { ?>
    <li <?php
  if ($this->item->id == $unit->id) {
    echo "class='active'";
  }
  ?>>
      <a href="<?php echo JRoute::_('index.php?option=com_accommodation&view=property&id=' . $unit->id) ?>">
  <?php echo $unit->title; ?><br />
          <?php if ($unit->occupancy && $unit->bedrooms) : ?>
            <span class="small">
              <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_UNIT_OCCUPANCY_BEDROOMS',$unit->occupancy,$unit->bedrooms ); ?>
            </span>
          <?php endif; ?>
      </a>
    </li>
<?php } ?>
</ul>
