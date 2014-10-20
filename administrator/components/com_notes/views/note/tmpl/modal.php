<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.framework');
JHtml::_('behavior.keepalive');
$input = JFactory::getApplication()->input;
$property_id = $input->get('property_id', '', 'int');
?>
<h1 class="page-header"><?php echo JText::sprintf('COM_NOTES_ADD_NOTE_FOR_PROPERTY', $property_id); ?></h1>

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
  <input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_('index.php?option=com_notes&tmpl=component&layout=modal&property_id=' . (int) $property_id, false)); ?>" />
  <?php echo JHtml::_('form.token'); ?>

  <button class="btn btn-primary">
    <?php echo Jtext::_('JSAVE') ?>
  </button>
</form>
