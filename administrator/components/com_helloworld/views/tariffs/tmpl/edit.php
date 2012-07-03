<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=availability&task=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="helloworld-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">		
		<legend><?php echo JText::_( 'COM_HELLOWORLD_HELLOWORLD_TARIFFS' ); ?></legend>
      <ul class="adminformlist">
        <?php foreach($this->form->getFieldset('tariffs') as $field): ?>
          <li><?php echo $field->label;echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>			
		</fieldset>
	</div>
  <div class="width-40 fltrt">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_HELLOWORLD_HELLOWORLD_ADDITIONAL_TARIFFS_DETAIL' ); ?></legend>
				<ul class="adminformlist">
					<?php foreach($this->form->getFieldset('additional-fields') as $field): ?>
						<li><?php echo $field->label;echo $field->input;?></li>
					<?php endforeach; ?>
				</ul>			
		</fieldset>
		
		<input type="hidden" name="task" value="tariffs.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
  

</form>	
