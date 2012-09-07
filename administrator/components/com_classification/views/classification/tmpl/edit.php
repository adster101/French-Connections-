<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

  // load tooltip behavior
  JHtml::_('behavior.tooltip');
  JHtml::_('behavior.formvalidation');
?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_classification&view=classification&layout=edit&id=' . (int) $this->item->id ); ?>" id="offer-form" method="post" name="adminForm">
		<div class="width-60 fltlft">
      <fieldset class="adminform">
        <legend><?php echo JText::_( 'Classification detail' ); ?></legend>
        <ul class="adminformlist">
          <?php foreach($this->form->getFieldset('classification') as $field): ?>
            <li><?php echo $field->label;echo $field->input;?></li>
          <?php endforeach; ?>
        </ul>
      </fieldset>
    </div>
    <div class="width-40 fltrt">
      <fieldset class="adminform">
        <legend><?php echo JText::_( 'Params' ); ?></legend>
      </fieldset>
    </div>
    <input type="hidden" name="task" value="classification.edit" />

		<?php echo JHtml::_('form.token'); ?>
</form>
