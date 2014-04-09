<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

// Add various bits of data to an array
$data = array();
$data['property'] = $this->item;
$data['progress'] = $this->progress;

// So we can pass them into our layout files
$tabs_layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
$progress_layout = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');

$fieldsets = $this->form->getFieldSets('citiestowns');
$amenities = $this->form->getGroup('amenities');
?>

<form action="<?php echo JRoute::_('index.php?option=com_rental&view=property&task=edit&property_id=' . (int) $this->item->property_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
  <?php echo JHtml::_('form.token'); ?>

  <div class="row-fluid">
   
        <div class="span12">
        <?php
        echo $progress_layout->render($data);
        echo $tabs_layout->render($data);
        ?>

        <fieldset>
          <legend><?php echo JText::_('COM_RENTAL_HELLOWORLD_ACCOMMODATION_LOCATION_DETAILS'); ?></legend>
          <div class="alert alert-notice">
            <?php echo JText::_('COM_RENTAL_HELLOWORLD_EDIT_LOCATION_INSTRUCTIONS'); ?>   
          </div>          
          <div class="control-group">
            <?php echo $this->form->getLabel('department'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('department'); ?>
            </div>
          </div>
          <div class="control-group">
            <?php echo $this->form->getLabel('city'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('city'); ?>
            </div>
          </div>
          <?php echo $this->form->getInput('map'); ?>
          <?php echo $this->form->getInput('latitude'); ?>
          <?php echo $this->form->getInput('longitude'); ?>


        </fieldset>
        <fieldset>
          <legend>Surrounding area</legend>
          <div class="alert alert-notice">
            <i class="icon-flag"></i> <?php echo JText::_('COM_RENTAL_HELLOWORLD_FIELD_LOCATION_DETAILS_DESC'); ?>
          </div>           
          <div class="control-group">
            <?php echo $this->form->getLabel('location_details'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('location_details'); ?>
            </div>
          </div>
          <div class="control-group">
            <?php echo $this->form->getLabel('activities'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('activities'); ?>
            </div>
          </div>
          <div class="control-group">
            <?php echo $this->form->getLabel('location_type'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('location_type'); ?>
            </div>
          </div>
          <div class="control-group">
            <?php echo $this->form->getLabel('distance_to_coast'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('distance_to_coast'); ?>
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>Getting there</legend>
          <div class="alert alert-notice">
            <i class="icon-flag"></i> <?php echo JText::_('COM_RENTAL_HELLOWORLD_FIELD_GETTING_THERE_DESC'); ?>
          </div>          
          <div class="control-group">
            <?php echo $this->form->getLabel('getting_there'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('getting_there'); ?>
            </div>
          </div>
          <div class="control-group">
            <?php echo $this->form->getLabel('airport'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('airport'); ?>
            </div>
          </div>
          <div class="control-group">
            <?php echo $this->form->getLabel('access'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('access'); ?>
            </div>
          </div>
        </fieldset>



        <fieldset>
          <legend>Local Amenities</legend>
          <div class="alert alert-notice">
            <i class="icon-flag"></i> You can specify additional local amenities here. If completed these will appear on your property listing
          </div>
          <?php foreach ($this->form->getGroup('amenities') as $field) : ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </fieldset>
      </div>

    </div>
  </div>
</div>



<?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>

  <?php echo $field->input; ?>
<?php endforeach; ?>

<input type="hidden" name="task" value="" />
<input type="hidden" name="next" value="<?php echo base64_encode(JRoute::_('index.php?option=com_rental&task=unitversions.edit&unit_id=' . (int) $this->progress[0]->unit_id, false)); ?>" />
</form>

