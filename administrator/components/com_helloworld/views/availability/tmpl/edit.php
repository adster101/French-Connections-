<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$params = $this->form->getFieldsets('availability');
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=availability&task=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="helloworld-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_HELLOWORLD_HELLOWORLD_AVAILABILITY' ); ?></legend>
			<?php //echo $this->calendar; ?>
			<ul class="adminformlist">
<?php foreach($this->form->getFieldset('availability') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>
		<input type="hidden" name="task" value="availability.edit" />
		<?php echo JHtml::_('form.token'); ?>
</form>