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
JHtml::_('behavior.keepalive');
?>
<form action="<?php echo JRoute::_('index.php?option=com_notes') ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
  <?php foreach ($this->form->getFieldSets() as $name => $fieldset): ?>
    <fieldset class="panelform">
      <?php foreach ($this->form->getFieldset() as $field) : ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </fieldset>
  <?php endforeach; ?>
  <input type="hidden" name="task" value="note.save" />
  <?php echo JHtml::_('form.token'); ?>
</form>
