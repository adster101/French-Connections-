<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$id = JRequest::getVar('id','0', 'GET', 'integer');
$parent_id = JRequest::getVar('parent_id','1', 'GET', 'integer');
?>


<form action="<?php echo JRoute::_('index.php?option=com_helloworld&task=images.upload&' . JSession::getFormToken() . '=1&id=' . (int) $id) . '&parent_id=' . $parent_id; ?>" method="post" name="imageUpload" id="tariffs-form">
  <div class="width-100">
    <fieldset class="adminform">		
      <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_UPLOAD_IMAGES'); ?></legend>
      <div id="image-queue">
          <?php foreach ($this->form->getFieldset('upload') as $field) { ?>
              <?php
                echo $field->input;
              ?>
          <?php } ?>
      </div>       
    </fieldset>
    <div class="clearfix fltrt">
      <button class="btn btn-large" id="explore">
        <i class="boot-icon-search boot-icon"></i> 
          <?php echo JText::_('COM_HELLOWORLD_IMAGES_BROWSE_IMAGES'); ?>
      </button>      
      <button class="btn btn-primary btn-large">
        <i class="boot-icon-upload boot-icon-white"></i> 
          <?php echo JText::_('COM_HELLOWORLD_IMAGES_UPLOAD_IMAGES'); ?>
      </button>
    </div>
  <?php echo JHtml::_('form.token'); ?>
  <?php foreach ($this->form->getFieldset('details') as $field) { ?>
    <?php echo $field->label; echo $field->input;?>   
  <?php }?>
      
</form>