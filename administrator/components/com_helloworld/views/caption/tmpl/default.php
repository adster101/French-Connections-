

<form method="post" name="adminForm" id="helloworld-form" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_helloworld&task=images.updatecaption&id=' . (int) $this->property_id) . '&file_id=' . (int) $this->file_id . '&' . JSession::getFormToken() . '=1'; ?>">
	<fieldset class="adminform">
  <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_EDIT_CAPTION') ?> </legend>
  <p><?php echo JText::_('COM_HELLOWORLD_IMAGES_EDIT_CAPTION_DESC') ?></p>

  <ul class="adminformlist clearfix">
<?php foreach($this->form->getFieldset('caption-details') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>  
  <hr />
        <button class="btn-primary btn btn-large fltrt">
    <i class="boot-icon-edit boot-icon-white"></i>
    <?php echo JText::_('COM_HELLOWORLD_IMAGES_EDIT_CAPTION') ?>
  </button>
		</fieldset
</form>