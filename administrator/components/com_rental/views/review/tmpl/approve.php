<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_rental'); ?>" id="adminForm" method="post" name="adminForm">
  <div>
    <fieldset class="adminform">
      <legend><?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_APPROVE_CHANGES', $this->id); ?></legend>
        <?php foreach ($this->form->getFieldset('message') as $field): ?>
            <?php
            echo $field->label;
            echo $field->input;
            ?>
        <?php endforeach; ?>
    </fieldset>
  </div>
  <input type="hidden" name="task" value="offer.edit" />
  <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
  <?php echo JHtml::_('form.token'); ?>
</form>
