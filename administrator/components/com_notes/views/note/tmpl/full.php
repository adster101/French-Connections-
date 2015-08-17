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
$property_id = $this->item->property_id; ?>
<form action="<?php echo JRoute::_('index.php?option=com_notes') ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

  <h1 class="page-header"><?php echo JText::sprintf('COM_NOTES_VIEWING_NOTE_ID', $property_id); ?></h1>


  <?php foreach ($this->form->getFieldSets() as $name => $fieldset): ?>

    <fieldset class="panelform">
      <?php foreach ($this->form->getFieldset() as $field) : ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->value; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </fieldset>
  <?php endforeach; ?>
  <?php echo JHtml::_('form.token'); ?>

  <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_notes&tmpl=component&layout=modal&property_id=' . (int) $property_id, false); ?>">
    <?php echo Jtext::_('JCANCEL') ?>
  </a>

</form>
