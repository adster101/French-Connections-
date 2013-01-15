<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.formvalidation');

?>
<form class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_enquiries'); ?>" id="adminForm" method="post" name="adminForm">

   <div id="j-main-container" class="span7">

    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_ENQUIRIES_ENQUIRY_DETAIL'); ?></legend>
      <?php foreach ($this->form->getFieldset('requestor') as $field): ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>         
      <?php endforeach; ?>
    </fieldset>
     <fieldset>
      <?php foreach ($this->form->getFieldset('details') as $field): ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>         
      <?php endforeach; ?>
     </fieldset>
     <fieldset>
      <?php foreach ($this->form->getFieldset('themessage') as $field): ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>         
      <?php endforeach; ?>
     </fieldset>
   </div>
  <div class="span5">
    <legend><?php echo JText::_('COM_ENQUIRIES_ENQUIRY_RESPOND_TO_ENQUIRY'); ?></legend>
     <fieldset>
      <?php foreach ($this->form->getFieldset('reply') as $field): ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>         
      <?php endforeach; ?>
     </fieldset>    
  </div>
  
<input type="hidden" name="task" value="" />

<?php echo JHtml::_('form.token'); ?>
</form>
