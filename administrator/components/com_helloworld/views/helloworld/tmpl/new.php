<?php

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

if ($this->items > 0) : 

?>

<h1><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING'); ?></h1>
<hr />
<div class="pre_message">
<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_SECOND_NEW_PROPERTY_BLURB'); ?>
</div>
<form method="post" name="adminForm" id="helloworld-form" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_helloworld&task=helloworld.woot') .  '&' . JUtility::getToken() . '=1'; ?>">
	<fieldset class="adminform">

  <ul class="adminformlist clearfix">
<?php foreach($this->form->getFieldset('properties') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>  
     
  </fieldset>
    <p class="">
  <button class="btn btn-primary fltrt">
    <?php echo JText::_('COM_HELLOWORLD_NEW_PROPERTY_PROCEED'); ?>
    <i class="boot-icon-forward boot-icon-white"></i>
  </button>
</p>   
</form>

<?php else : ?>
<h1><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING'); ?></h1>
<hr />
<div class="pre_message">
<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING_BLURB'); ?>
</div>
<hr />
<p class="">
  <a class="btn btn-primary fltrt" href="index.php?option=com_helloworld&task=helloworld.edit">
    <?php echo JText::_('COM_HELLOWORLD_NEW_PROPERTY_PROCEED'); ?>
  </a>
</p>
<?php endif; ?>