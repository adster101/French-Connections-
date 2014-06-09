<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.formvalidation');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JForm::addFieldPath(JPATH_SITE . '/libraries/frenchconnections/fields');
?>   
<form class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_enquiries'); ?>" id="adminForm" method="post" name="adminForm">

  <?php if (!empty($this->sidebar)): ?>
    <div class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div class="span5">
    <?php else : ?>
      <div id="j-main-container" class="span6">
      <?php endif; ?>

      <fieldset>
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

    </div>


    <div class="span6">
      <fieldset >       
        <legend><?php echo JText::_('COM_ENQUIRIES_ENQUIRY_RESPOND_TO_ENQUIRY'); ?></legend>
        <?php foreach ($this->form->getFieldset('reply') as $field): ?>
          <div class="control-group">
            <?php echo $field->label; ?>
            <div class="controls">
              <?php echo $field->input; ?>
            </div>
          </div>   
        <?php endforeach; ?>
      </fieldset>
      <fieldset class="form-inline">       
        <?php echo $this->form->getInput('cc_message'); ?>  
        <?php echo $this->form->getLabel('cc_message'); ?>
        <hr />
        <button class="btn" onclick="Joomla.submitbutton('enquiry.reply')" href="#">
          <i class="icon-save ">
          </i>
          <?php echo JText::_('COM_ENQUIRIES_ENQUIRY_REPLY'); ?>
        </button>
      </fieldset>    
    </div>
  </div>
</div>
<?php foreach ($this->form->getFieldset('hidden') as $field): ?>
  <?php echo $field->input; ?>
<?php endforeach; ?>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>          
