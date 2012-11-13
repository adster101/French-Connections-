<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

// Open flag toggles the first accordion panel to be open. Subsequent ones are 'shut' by default.
$open = true;
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=facilities&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate ">
  <fieldset>
    <legend><?php echo JText::_('COM_HELLOWORLD_FACILITIES_LEGEND'); ?></legend>
    <p><?php echo JText::_('COM_HELLOWORLD_FACILITIES_BLURB'); ?></p>

    <div class="accordion" id="accordion1">
      <?php foreach ($this->form->getFieldSets() as $name => $fieldset): ?>
        <div class="accordion-group">
          <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#<?php echo $name; ?>">
              <?php echo JText::_($fieldset->description); ?>
            </a>      
          </div>
          <div id="<?php echo $name ?>" class="accordion-body collapse<?php
            if ($open) {
              echo ' in';
              $open = false;
            }
              ?>">
            <div class="accordion-inner">        
              <fieldset class="panelform">
                <?php foreach ($this->form->getFieldset($name) as $field) : ?>
                  <p><?php echo $field->label; ?></p>
                  <?php echo $field->input; ?>
                <?php endforeach; ?>
              </fieldset>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <input type="hidden" name="task" value="availability.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>