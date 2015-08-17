<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');

$language = JFactory::getLanguage();

$fieldsets = $this->form->getFieldSets();
?>
<div class="row-fluid">
  <div class="span12">
    <form action="<?php echo JRoute::_('index.php?option=com_rental&view=autorenewals&id=' . (int) $this->id) ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
      <?php foreach ($fieldsets as $fieldset) : ?>
        <fieldset>
          <?php echo JText::_($fieldset->description); ?>
          <legend><?php echo JText::_($fieldset->label); ?></legend>
          <?php foreach ($this->form->getFieldset($fieldset->name) as $field) : ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>
            <hr />
          <?php endforeach; ?>
        </fieldset>
      <?php endforeach; ?>
      <?php
      $actions = new JLayoutFile('frenchconnections.property.actions');
      echo $actions->render(array());
      ?>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="jform[id]" value="11" />
      <?php echo JHtml::_('form.token'); ?>
    </form>
  </div>
</div>
