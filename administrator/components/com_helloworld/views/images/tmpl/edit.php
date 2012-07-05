<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=availability&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="tariffs-form" class="form-validate">
  <div class="width-50 fltlft">
    <fieldset class="adminform">		
      <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_LIBRARY'); ?></legend>
      <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset('upload') as $field) { ?>
          <li>
            <?php
              echo $field->label;
              echo $field->input;
            ?>
          </li>
        <?php } ?>
      </ul>			        
    </fieldset>
  </div>
  <div class="width-50 fltrt">
    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_GALLERY'); ?></legend>
    </fieldset>
    <input type="hidden" name="task" value="tariffs.edit" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>	
