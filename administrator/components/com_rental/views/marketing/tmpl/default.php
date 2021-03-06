<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.framework');
JHtml::_('behavior.keepalive');
$fieldsets = $this->form->getFieldSets();
?>
<form 
  action="<?php echo JRoute::_('index.php?option=com_rental&task=marketing.save&property_id=' . (int) $this->id) ?>" 
  method="post" 
  name="adminForm" 
  id="adminForm" 
  class="form-validate form-vertical">
  <div class="row-fluid">
    <div class="span12">
      <?php foreach ($fieldsets as $fieldset): ?>
        <?php if ($fieldset->name != 'hidden') : ?>  
          <fieldset class="panelform">
            <legend>
              <?php echo JText::_($fieldset->label); ?>
            </legend>
            <?php echo JText::_($fieldset->description); ?>
            <?php foreach ($this->form->getFieldset($fieldset->name) as $field) : ?>
              <div class="control-group">
                <div class='control-label'>
                  <?php echo $field->label; ?>
                </div>
                <div class="controls">
                  <?php echo $field->input; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </fieldset> 
        <?php endif; ?>
      <?php endforeach; ?>
      <?php foreach ($this->form->getFieldset('hidden') as $field) : ?>
        <?php echo $field->input; ?>
      <?php endforeach; ?>

      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>
      <?php
      $actions = new JLayoutFile('frenchconnections.property.actions');
      echo $actions->render(array());
      ?>
    </div>
  </div>
</form>

