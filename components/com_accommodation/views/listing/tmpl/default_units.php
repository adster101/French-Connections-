<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$input = JFactory::getApplication()->input;
$preview = $input->get('preview','','int');
$append = '';
if ((int) $preview && $preview == 1) {
  $append = '&preview=1';
}

?>

<ul class="nav nav-tabs">
  <?php foreach ($this->units as $unit) { ?>
    <li <?php
  if ($this->item->unit_id == $unit->id) {
    echo "class='active'";
  }
  ?>>
      <a title="<?php echo $unit->unit_title ?>" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $unit->property_id . '&unit_id=' . (int) $unit->id) . $append ?>">
        <?php echo JHtml::_('string.truncate', $unit->unit_title, 25); ?><br />
          <?php if ($unit->occupancy && $unit->bedrooms) : ?>
            <span class="small">
              <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_UNIT_OCCUPANCY_BEDROOMS',$unit->occupancy,$unit->bedrooms ); ?>
            </span>
          <?php endif; ?>
      </a>
    </li>
<?php } ?>
</ul>

