<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

// Add various bits of data to an array
$data = array();
//$data['property'] = $this->item;
//$data['progress'] = $this->progress;
// So we can pass them into our layout files
//$tabs_layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
//$progress_layout = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');

$fieldsets = $this->form->getFieldSets();
?>
<form action="<?php echo JRoute::_('index.php?option=com_realestate&view=property&task=edit&realestate_property_id=' . (int) $this->item->realestate_property_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
  <?php echo JHtml::_('form.token'); ?>
  <div class="row-fluid">
    <?php if (!empty($this->sidebar)): ?>
      <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        <?php //echo JText::_('COM_REALESTATE_LISTING_DETAILS_HELP'); ?>
      </div>
      <div id="" class="span10">
      <?php else : ?>
        <div class="span12">
        <?php endif; ?>
        <?php
        //echo $progress_layout->render(array('status'=>$this->status));
        //echo $tabs_layout->render($data);
        ?>
        <?php foreach ($fieldsets as $fieldset): ?>
          <fieldset class="adminform">       
            <?php if ($fieldset->name != 'hidden-details'): ?>
              <legend><?php echo JText::_($fieldset->label); ?></legend>
              <?php foreach ($this->form->getFieldset($fieldset->name) as $field): ?>
                <div class="control-group">
                  <?php echo $field->label; ?>
                  <div class="controls">
                    <?php echo $field->input; ?>
                  </div>
                </div>       
              <?php endforeach; ?> 
            </fieldset>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>
      <?php echo $field->input; ?>
    <?php endforeach; ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="next" value="<?php echo base64_encode(JRoute::_('index.php?option=com_rental&task=unitversions.edit&unit_id=' . (int) $this->status->unit_id, false)); ?>" />
</form>

