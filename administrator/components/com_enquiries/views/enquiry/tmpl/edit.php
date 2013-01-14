<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.formvalidation');

?>
<form class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_specialoffers&layout=edit&id=' . (int) $this->item->id); ?>" id="adminForm" method="post" name="adminForm">

   <div id="j-main-container" class="span12">

    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_SPECIALOFFER_DETAILS'); ?></legend>
      <?php foreach ($this->form->getFieldset('specialoffer') as $field): ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>         
      <?php endforeach; ?>
<?php foreach ($this->form->getFieldset('publishing') as $field): ?>
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
<input type="hidden" name="task" value="" />

<?php echo JHtml::_('form.token'); ?>
</form>
