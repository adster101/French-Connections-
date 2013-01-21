<?php

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

if ($this->items > 0) : 

?>

<div class="pre_message">
<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_SECOND_NEW_PROPERTY_BLURB'); ?>
</div>
	<fieldset class="adminform">
    <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_SECOND_NEW_PROPERTY_LEGEND'); ?>
</legend>
  <ul class="adminformlist clearfix">
<?php foreach($this->form->getFieldset('properties') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>  
     
  </fieldset>
   
		<?php echo JHtml::_('form.token'); ?>


<?php else : ?>
<div class="pre_message">
<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING_FIRST_PROPERTY_BLURB'); ?>
</div>
<hr />
<?php endif; ?>

