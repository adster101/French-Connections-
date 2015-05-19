<?php
/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// no direct access
defined('_JEXEC') or die;

$fieldsets = $this->form->getFieldSets();
?>
<form action="<?php echo JRoute::_('index.php?option=com_fcadmin&view=images&unit_id=' . (int) $this->unit_id); ?>" method="post" name="invoiceForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal clearfix">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container" class="span12">
      <?php endif; ?> 
      <?php foreach ($fieldsets as $fieldset) : ?>
        <fieldset class="form-inline">
          <legend>
            <?php echo JText::_($fieldset->label); ?>
          </legend>
          <?php foreach ($this->form->getFieldSet($fieldset->name) as $field) : ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </fieldset>
      <?php endforeach; ?>   
      <div class="form-actions">

        <button type="submit" class="btn btn-primary" data-loading-text="FCGLOBAL_PLEASE_WAIT">
          <span class="icon-image"></span>
          Retrieve images</button>
      </div>
    </div>
    <?php echo JHtml::_('form.token'); ?>
</form>
<?php if (count($this->images) > 0) : ?>
  <?php for ($i = 0, $n = count($this->images); $i < $n; $i++) : ?>
      <?php echo JHtml::_('image', $this->baseURL . '/' . $this->images[$i]->path_relative, JText::sprintf('COM_MEDIA_IMAGE_TITLE', $this->images[$i]->title, JHtml::_('number.bytes', $this->images[$i]->size)), array('width' => $this->images[$i]->width, 'height' => $this->images[$i]->height)); ?>
      <?php echo JHtml::_('string.truncate', $this->images[$i]->name, 25, false); ?>
    <hr />
  <?php endfor; ?>
<?php else : ?>
  <div class="alert alert-info"><?php echo JText::_('COM_FCADMIN_NO_IMAGES_FOUND'); ?></div>
<?php endif; 

