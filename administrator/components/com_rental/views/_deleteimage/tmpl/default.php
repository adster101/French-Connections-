<fieldset class="adminform">
  <legend><?php echo JText::_('COM_RENTAL_IMAGES_DELETE_IMAGE') ?> </legend>
  <p><?php echo JText::_('COM_RENTAL_IMAGES_DELETE_IMAGE_DESC') ?></p>
  <hr />
  <a class="btn-danger btn btn-large fltrt" href="<?php echo JRoute::_('index.php?option=com_rental&task=images.delete&id=' . (int) $this->id) . '&file=' . (int) $this->file_id . '&' . JSession::getFormToken() . '=1'; ?>">
    <i class="boot-icon-trash boot-icon-white"></i>
    <?php echo JText::_('COM_RENTAL_IMAGES_DELETE') ?>
  </a>
</fieldset>
