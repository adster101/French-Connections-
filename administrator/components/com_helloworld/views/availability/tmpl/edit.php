<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=availability&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
  <div class="row-fluid">
    <div class="span4">
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ADDITIONAL_AVAILABILITY_DETAIL'); ?></legend>
        <?php foreach ($this->form->getFieldset('changeover-day') as $field): ?>
          <div class="control-group">

            <?php echo $field->label; ?>
            <div class="controls">
              <?php echo $field->input; ?>
            </div></div>
        <?php endforeach; ?>       
      </fieldset>
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_UPDATE_AVAILABILITY'); ?></legend>
        <?php foreach ($this->form->getFieldset('availability') as $field): ?>
          <div class="control-group">

            <?php echo $field->label; ?>
            <div class="controls">
              <?php echo $field->input; ?>
            </div></div>
        <?php endforeach; ?>
      </fieldset>
      <input type="hidden" name="task" value="availability.edit" />
      <?php echo JHtml::_('form.token'); ?>
    </div>
    <div class="span8 ">
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_AVAILABILITY'); ?></legend>
        <?php echo $this->calendar; ?>
      </fieldset>
    </div>
  </div>
  <?php foreach ($this->form->getFieldset('additional-fields') as $field): ?>
    <?php
    echo $field->label;
    echo $field->input;
    ?>
<?php endforeach; ?>
</form>
