<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_reviews&layout=edit&id=' . (int) $this->item->id); ?>" id="adminForm" method="post" name="adminForm">

  <div id="j-sidebar-container" class="span2">
 
  </div>
  <div id="j-main-container" class="span10">

    <fieldset class="adminform">
      <legend><?php echo JText::_('Attribute detail'); ?></legend>
      <?php foreach ($this->form->getFieldset('review') as $field): ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>         
      <?php endforeach; ?>
    </fieldset>
  </div>
</div>
<input type="hidden" name="task" value="review.edit" />

<?php echo JHtml::_('form.token'); ?>
</form>
