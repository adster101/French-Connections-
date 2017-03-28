<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('dropdown.init');

$canDo = RentalHelper::getActions();
$canEditOwn = $canDo->get('core.edit.own');
$canPublish = $canDo->get('core.edit.state');
?>


<table class="table table-striped">
  <thead>
    <tr>
      <th>
      </th>
    </tr>
  </thead>
  <tr>
    <td width="15%">
      <strong>Property details</strong>
    </td>
    <td>
      <?php echo RentalHelper::progressButton($this->items[0]->id, $this->items[0]->unit_id, 'propertyversions', 'edit', 'COM_RENTAL_HELLOWORLD_PROPERTY_DETAILS', $this->items[0], 'property_id', 'btn') ?>
      <?php echo RentalHelper::progressButton($this->items[0]->id, $this->items[0]->unit_id, 'contactdetails', 'edit', 'COM_RENTAL_SUBMENU_MANAGE_CONTACT_DETAILS', $this->items[0], 'property_id', 'btn') ?>
    </td>
  </tr>
  <tfoot>
    <tr>
      <td colspan="7"></td>
    </tr>
  </tfoot>
</table>

<table class="table table-striped" id="articleList">
  <thead>
    <tr>
      <th colspan="2">
        Accommodation units
      </th>
      <?php if ($canDo->get('core.edit.state')) : ?>
        <th>
          <?php echo JText::_('COM_RENTAL_HELLOWORLD_HEADING_ACTIVE'); ?>
        </th>
      <?php endif; ?>
      <th>
        Ordering
      </th>                            
      <th width="2%">
        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
      </th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->items as $i => $item): ?>
      <?php if ($canEditOwn) : ?>
        <tr>
          <td width="15%">
            <strong><?php echo JText::_($item->unit_title) ?></strong>
          </td>
          <td>
            <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'unitversions', 'edit', 'COM_RENTAL_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id', 'btn') ?>
            <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'images', 'manage', 'IMAGE_GALLERY', $item, 'unit_id', 'btn') ?>
            <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'tariffs', 'edit', 'COM_RENTAL_SUBMENU_MANAGE_TARIFFS', $item, 'unit_id', 'btn') ?>
            <?php echo RentalHelper::progressButton($item->id, $item->unit_id, 'availability', 'manage', 'COM_RENTAL_SUBMENU_MANAGE_AVAILABILITY', $item, 'unit_id', 'btn') ?>
          </td>
          <?php if ($canDo->get('core.edit.state')) : ?>
            <td>
              <?php echo JHtml::_('jgrid.published', $item->published, $i, 'units.', $canPublish, 'cb', $item->created_on); ?>
            </td>
          <?php endif; ?>                  
          <td>
            <?php echo $this->pagination->orderUpIcon($i, true, 'units.orderup', 'JLIB_HTML_MOVE_UP', 1); ?>
            <?php echo $this->pagination->orderDownIcon($i, count($this->items), true, 'units.orderdown', 'JLIB_HTML_MOVE_DOWN', 1); ?>
          </td>
          <td>
            <?php echo JHtml::_('grid.id', $i, $item->unit_id); ?>
          </td>
        </tr>
      <?php else : ?>
      <?php endif; ?>
    <?php endforeach; ?>
  </tbody>
</table>
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

