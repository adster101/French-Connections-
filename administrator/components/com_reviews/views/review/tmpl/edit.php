<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.core');
?>
<form class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_reviews&layout=edit&id=' . (int) $this->item->id); ?>" id="adminForm" method="post" name="adminForm">

  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>

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

</div>
