<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.tabstate');

?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_classification&view=classification&layout=edit&id=' . (int) $this->item->id); ?>" id="adminForm" method="post" name="adminForm">
  <div class="row-fluid">	
    <div class="span12">
      <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'main')); ?>
      <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'main', JText::_('COM_CLASSIFICATION_MAIN_DETAILS', true)); ?>
      <fieldset class="adminform">
        <legend><?php echo JText::_('Classification detail'); ?></legend>
        <?php foreach ($this->form->getFieldset('classification') as $field): ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>         
        <?php endforeach; ?>
      </fieldset>

      <?php echo JHtml::_('bootstrap.endTab'); ?>
      <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'property', JText::_('COM_CLASSIFICATION_PROPERTY_TYPE_INFO', true)); ?>

      <?php foreach ($this->property_types as $type) : ?>
        <?php $name = JStringNormalise::toDashSeparated(JApplication::stringURLSafe($type->title)); ?>
        
      <fieldset class="adminform">
        <legend><?php echo $type->title; ?></legend>
      
          <?php foreach ($this->form->getGroup($name) as $field): ?>

              <div class="control-group">
                <?php echo $field->label; ?>
                <div class="controls">
                  <?php echo $field->input; ?>
                </div>
              </div>         

          <?php endforeach; ?>
      <?php endforeach; ?>

      <?php echo JHtml::_('bootstrap.endTab'); ?>
      <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'meta', JText::_('COM_CLASSIFICATION_META_DATA', true)); ?>
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_CLASSIFICATION_META_DATA'); ?></legend>
        <?php foreach ($this->form->getFieldset('meta') as $field): ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>         
        <?php endforeach; ?>
      </fieldset>
      <?php echo JHtml::_('bootstrap.endTab'); ?>
      <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>
  </div>
  <input type="hidden" name="task" value="classification.edit" />
  <?php echo JHtml::_('form.token'); ?>
</form>
<?php new JForm(); ?>