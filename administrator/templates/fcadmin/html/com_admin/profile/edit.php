<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Include the helloworld helper class
require_once(JPATH_ADMINISTRATOR.'/components/com_helloworld/helpers/helloworld.php');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'profile.cancel' || document.formvalidator.isValid(document.id('profile-form'))) {
			Joomla.submitform(task, document.getElementById('profile-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_admin&view=profile&layout=edit&id='.$this->item->id); ?>" method="post" name="adminForm" id="profile-form" class="form-validate" enctype="multipart/form-data">
	<div class="width-50 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_ADMIN_USER_ACCOUNT_DETAILS'); ?></legend>
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('user_details') as $field) :?>
				<li><?php echo $field->label; ?>
				<?php echo $field->input; ?></li>
			<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>

	<div class="width-50 fltrt">
		<?php
          echo JHtml::_('sliders.start');   

      if (array_key_exists('profile', $fieldsets)) :
      echo JHtml::_('sliders.panel', JText::_($fieldsets['profile']->label), $fieldsets['profile']->name);    
    ?>
       
		<fieldset class="panelform">
      <ul class="adminformlist">
      <?php foreach($this->form->getFieldset('profile') as $field): ?>
        <?php if ($field->hidden): ?>
          <?php echo $field->input; ?>
        <?php else: ?>
          <li><?php echo $field->label; ?>
          <?php echo $field->input; ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
      </ul>
		</fieldset>
    
    <?php 
      endif;
      if(!HelloWorldHelper::isOwner()) :
       
        echo JHtml::_('sliders.panel', JText::_($fieldsets['settings']->label), $fieldsets['settings']->name); 
      
    ?>
      <fieldset class="panelform">
        <ul class="adminformlist">
        <?php foreach($this->form->getFieldset('settings') as $field): ?>
          <?php if ($field->hidden): ?>
            <?php echo $field->input; ?>
          <?php else: ?>
            <li><?php echo $field->label; ?>
            <?php echo $field->input; ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
        </ul>
      </fieldset> 
    <?php endif; ?>
    
		<?php echo JHtml::_('sliders.end'); ?>

		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
