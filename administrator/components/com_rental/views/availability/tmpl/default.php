<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$data = array('item' => $this->unit, 'progress' => $this->progress);

// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');
$languages = RentalHelper::getLanguages();

// Process the units into a keyed array (useful so we can get at individual units)
// Easier to just foreach ?
$units = RentalHelper::getUnitsById($data['progress']);

// Determine the unit id, if a new unit unit_id = 0 - the listing id is then used as parent in the create unit view
($view == 'propertyversions') ? $unit_id = key($units) : $unit_id = $input->get('unit_id', '0', 'int');

// Set the item which is used below to output the tabs
$item = (!empty($unit_id)) ? $units[$unit_id] : RentalHelper::getEmptyUnit($listing_id);

$availability_last_updated = (!empty($item->availability_last_updated_on)) ? $item->availability_last_updated_on : '';
?>

<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span8">
    <?php else : ?>
      <div id="j-main-container" class="span10">
      <?php endif; ?>
      <?php
      $progress = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
      $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
      echo $progress->render($data);
      echo $layout->render($data);
      ?>
      <legend><?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_AVAILABILITY', $this->unit->unit_title); ?></legend>
      <div class="clear alert alert-notice">
        <?php echo JText::_('COM_RENTAL_HELLOWORLD_AVAILABILITY_INSTRUCTIONS'); ?>
      </div>
      <div class="row-fluid">
        <div class="span8">
          <p class="pull-left">
            <?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_AVAILABILITY_LAST_UPDATED', $availability_last_updated); ?>
          </p>
        </div>
        <div class="span4">
          <table class="key">
            <tr>
              <td class="available"></td>
              <td>&nbsp;<?php echo JText::_('COM_RENTAL_HELLOWORLD_AVAILABILITY_AVAILABLE') ?></td>
              <td>&nbsp;</td>
              <td class="unavailable">1</td>
              <td>&nbsp;<?php echo JText::_('COM_RENTAL_HELLOWORLD_AVAILABILITY_UNAVAILABLE') ?></td>
            </tr>
          </table>
        </div>
      </div>

      <?php echo $this->calendar; ?>
      <form action="<?php echo JRoute::_('index.php?option=com_rental&view=availability&unit_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
        <div id="availabilityModal" class="hide fade modal">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">
              <?php echo JText::_('COM_RENTAL_HELLOWORLD_UPDATE_AVAILABILITY') ?>
            </h3>
          </div>
          <div class="modal-body">
            <fieldset class="adminform">
              <?php foreach ($this->form->getFieldset('availability') as $field): ?>
                <div class="control-group">
                  <?php echo $field->label; ?>
                  <div class="controls">
                    <?php echo $field->input; ?>
                  </div></div>
              <?php endforeach; ?>
            </fieldset>
            <input type="hidden" name="task" value="availability.apply" /> 
            <input type="hidden" name="jform[property_id]" value=<?php echo $this->unit->property_id ?> /> 

            <?php echo JHtml::_('form.token'); ?>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary">
              <?php echo JText::_('JSAVE') ?>
            </button>
            <button class="btn" data-dismiss="modal" aria-hidden="true">
              <?php echo JText::_('JCANCEL') ?>
            </button>

          </div>
        </div>
      </form>

    </div>
    <div class="span2">
      <div class="well well-small">
        <h3>Availability calendar</h3>
        <ul>
          <li>Update yo availability, fool!</li>
        </ul>
      </div>
    </div>
  </div>
