<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// Here we have an additional form which handles the AJAX image uploader for the property.
// Second form submits as usual to the default form for this controller
?>


<form action="<?php echo JRoute::_('index.php?option=com_helloworld&task=images.upload&'. JUtility::getToken() .'=1&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="tariffs-form" class="form-validate">
  <div class="width-50 fltlft">
    <fieldset class="adminform">		
      <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_UPLOAD_IMAGES'); ?></legend>
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
  <?php echo JHtml::_('form.token'); ?>

</form>

<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="tariffs-form" class="form-validate">
  <div class="width-50 fltrt">
    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_IMAGE_GALLERY'); ?></legend>
      <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset('image-gallery') as $field) { ?>
          <li>
            <?php
              echo $field->label;
              echo $field->input;
            ?>
          </li>
        <?php } ?>
      </ul>	
    </fieldset>
    <input type="hidden" name="task" value="images.edit" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>	
