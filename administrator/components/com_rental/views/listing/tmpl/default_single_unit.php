<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$canDo = RentalHelper::getActions();
$canEditOwn = $canDo->get('core.edit.own');
?>

<?php foreach ($this->items as $i => $item):
  ?>
  <?php if ($canEditOwn) : ?>
    <tr>
      <td>
        <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'propertyversions', 'edit', 'COM_RENTAL_HELLOWORLD_PROPERTY_DETAILS', $item, 'property_id', 'btn') ?>
        <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'unitversions', 'edit', 'COM_RENTAL_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id', 'btn') ?>
        <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'images', 'manage', 'IMAGE_GALLERY', $item, 'unit_id', 'btn') ?>
        <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'tariffs', 'edit', 'COM_RENTAL_SUBMENU_MANAGE_TARIFFS', $item, 'unit_id', 'btn') ?>
        <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'availability', 'manage', 'COM_RENTAL_SUBMENU_MANAGE_AVAILABILITY', $item, 'unit_id', 'btn') ?>
        <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'contactdetails', 'edit', 'COM_RENTAL_SUBMENU_MANAGE_CONTACT_DETAILS', $item, 'property_id', 'btn') ?>
      </td>
    </tr>
  <?php endif; ?>
<?php endforeach; ?>