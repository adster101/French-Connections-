<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<form method="post" name="adminForm" id="adminForm" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_helloworld&task=helloworld.addnew') . '&' . JSession::getFormToken() . '=1'; ?>">

  <fieldset class="adminform">
    <h4><?php echo JText::_('COM_HELLOWORL_HELLOWORLD_NEW_PROPERTY_CHOOSE_CREATED_BY'); ?></h4>
    <?php foreach ($this->form->getFieldset('owner') as $field): ?>
      <p><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_PROPERTY_CHOOSE_OWNER_DESC'); ?></p>

      <div class="control-group">
        <div class="controls">
          <?php
          echo $field->label;
          echo $field->input;
          ?>
        </div>
      </div>
<?php endforeach; ?>

  </fieldset>
</fieldset>


<hr />

<button class="btn btn-large btn-primary" href="#" onclick="Joomla.submitbutton('helloworld.addnew')">
<?php echo JText::_('COM_HELLOWORLD_NEW_PROPERTY_PROCEED'); ?>

  <i class="icon-next ">
  </i>
</button>	
<input type="hidden" name="task" value="" />

<?php echo JHtml::_('form.token'); ?>
</form>

