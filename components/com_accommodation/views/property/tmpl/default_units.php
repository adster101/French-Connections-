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
          <?php if ($this->item->occupancy) : ?>

          <small><?php echo JText::_('COM_ACCOMMODATION_SITE_OCCUPANCY'); ?>
    <?php echo $unit->occupancy; ?></small>
    <?php endif; ?>
      </a>
    </li>
<?php } ?>
</ul>

