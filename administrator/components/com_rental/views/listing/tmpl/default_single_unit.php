<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$canDo = RentalHelper::getActions();
$canEditOwn = $canDo->get('core.edit.own');
$canPublish = $canDo->get('core.edit.state');
?>

<?php foreach ($this->items as $i => $item):
  ?>
  <?php if ($canEditOwn) : ?>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>
          </th>
        </tr>
      </thead>
      <tr>
        <td>
          <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'propertyversions', 'edit', 'COM_RENTAL_HELLOWORLD_PROPERTY_DETAILS', $item, 'property_id', 'btn') ?>
          <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'unitversions', 'edit', 'COM_RENTAL_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id', 'btn') ?>
          <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'images', 'manage', 'IMAGE_GALLERY', $item, 'unit_id', 'btn') ?>
          <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'tariffs', 'edit', 'COM_RENTAL_SUBMENU_MANAGE_TARIFFS', $item, 'unit_id', 'btn') ?>
          <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'availability', 'manage', 'COM_RENTAL_SUBMENU_MANAGE_AVAILABILITY', $item, 'unit_id', 'btn') ?>
          <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'contactdetails', 'edit', 'COM_RENTAL_SUBMENU_MANAGE_CONTACT_DETAILS', $item, 'property_id', 'btn') ?>
        </td>
        <?php if ($canDo->get('core.edit.state') && !empty($this->activeFilters)) : ?>
          <td>
            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'units.', $canPublish, 'cb', $item->created_on); ?>
          </td>
          <td>
            <?php echo JHtml::_('grid.id', $i, $item->unit_id); ?>
          </td>
        <?php endif; ?>   
      </tr> 
      <tfoot>
        <tr>
          <td colspan="7"></td>
        </tr>
      </tfoot>
    </table>
  <?php endif; ?>
<?php endforeach; ?>
<?php if ($canDo->get('core.edit.state')) : ?>
  <div class="js-stools-container-filters hidden-phone clearfix">
    <?php
    $data['view'] = $this;
    echo JLayoutHelper::render('joomla.searchtools.default.filters', $data);
    ?>
  </div>
<?php endif; ?>
<input type="hidden" name="extension" value="<?php echo 'com_rental'; ?>" />
<input type="hidden" name="boxchecked" value="" />