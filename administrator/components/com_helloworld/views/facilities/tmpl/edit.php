<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$params = $this->form->getFieldsets('params'); ?>

      <?php foreach ($this->form->getFieldSets('facilities') as $name => $fieldset): ?>
            <?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
              <p class="tip"><?php echo $this->escape(JText::_($fieldset->description)); ?></p>
            <?php endif; ?>
            <fieldset class="panelform" >
              <ul class="adminformlist">
                <?php foreach ($this->form->getFieldset($name) as $field) : ?>
                  <li><?php echo $field->label; ?><?php echo $field->input; ?></li>
                <?php endforeach; ?>
              </ul>
            </fieldset>
          <?php endforeach; ?>