<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<form class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_featuredproperties&view=featuredproperty&layout=edit&id=' . (int) $this->item->id); ?>" id="adminForm" method="post" name="adminForm">
  <?php if (!empty($this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <fieldset class="adminform">
        <legend><?php echo JText::_('Featured property details'); ?></legend>
        <?php foreach ($this->form->getFieldset('featured-property') as $field): ?>
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

  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
  Joomla.submitbutton = function(task)
  {
    if (task == 'article.cancel' || document.formvalidator.isValid(document.id('adminForm')))
    {
      Joomla.submitform(task, document.getElementById('adminForm'));
    }
  }
</script>