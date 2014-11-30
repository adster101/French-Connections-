<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_import'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container" class="span12">
      <?php endif; ?> 
    <fieldset class="adminform">
      <legend><?php echo JText::_('Choose an import file'); ?></legend>
      <p>Import a list of users from a CSV file into the #__helloworld table. Sweet!</p>
      <input class="input_box" id="install_package" name="import_file" type="file" size="57" />
      <?php foreach ($this->form->getFieldset('import') as $field): ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>         
      <?php endforeach; ?>
    </fieldset>
  </div>
  <input type="hidden" name="task" value="" />

  <?php echo JHtml::_('form.token'); ?>
</form>

