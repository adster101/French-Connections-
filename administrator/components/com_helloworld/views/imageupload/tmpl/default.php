<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$id = JRequest::getVar('id','0', 'GET', 'integer');
$parent_id = JRequest::getVar('parent_id','1', 'GET', 'integer');



print_r($_GET);?>
<style>
  /* Override the label width inline for now. Sort this out later */
  fieldset.adminform label {
    min-width:50px;
  }

</style>

<form action="<?php echo JRoute::_('index.php?option=com_helloworld&task=images.upload&' . JUtility::getToken() . '=1&id=' . (int) $id) . '&parent_id=' . $parent_id; ?>" method="post" name="inageUpload" id="tariffs-form">
  <div class="width-100">
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