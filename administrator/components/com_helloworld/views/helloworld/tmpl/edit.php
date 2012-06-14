<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=helloworld&task=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="helloworld-form" class="form-validate">



<p>
<?php
	echo JHTML::_('select.genericlist', $this->languages, 'Language', 'onchange="submitbutton(\'changeLanguage\')"', 'value', 'text', $this->lang);
	echo JText::_('COM_HELLOWORLD_YOU_ARE_EDITING_IN') . '<strong>&nbsp;' . $this->lang . '</strong>';

?>
</p>
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_HELLOWORLD_HELLOWORLD_DETAILS' ); ?></legend>
			<ul class="adminformlist">
<?php foreach($this->form->getFieldset('details') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>
 
	<div class="width-40 fltrt">
	
		
		<?php echo JHtml::_('sliders.start', 'helloworld-slider'); ?>	
		
		<?php echo JHtml::_('sliders.panel', JText::_('Blah'), $name.'-params');?>
<fieldset class="panelform">
		<ul class="adminformlist">
		<?php foreach($this->form->getFieldset('owner') as $field): ?>
		
			<li><?php echo $field->label;echo $field->input;?></li>
			
		<?php endforeach; ?>
				</ul>
	</fieldset>	

<?php foreach ($params as $name => $fieldset): ?>
		<?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
	<?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
		<p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
	<?php endif;?>
		<fieldset class="panelform" >
			<ul class="adminformlist">
	<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
	<?php endforeach; ?>
			</ul>
		</fieldset>
<?php endforeach; ?>
 
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
 
	<div>
		<input type="hidden" name="task" value="helloworld.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
