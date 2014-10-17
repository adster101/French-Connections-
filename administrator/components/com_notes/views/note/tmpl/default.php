<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JHtml::_('behavior.framework');
JHtml::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rental'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
  <fieldset class="filter">
    <div id="filter-bar" class="btn-toolbar">
        <a class="btn" href="javascript:history.back();"><i class="icon icon-backward-2"></i>&nbsp;Back</a>     
        <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_rental&task=note.add&layout=edit&tmpl=component') ?>">
          <i class="icon icon-plus-2"></i>&nbsp;Add note</a>
    </div>
  </fieldset>
  <hr />
  <fieldset>
    <div class="control-group">
      <div class="control-label">
        <strong>
          <?php echo JText::_('COM_RENTAL_NOTE_FIELD_DATE_TIME_LABEL'); ?>
        </strong>
      </div>
      <div class="controls">
        <?php echo JHtml::_('date', $this->item->created_time); ?>
      </div>
    </div>
    <div class="control-group">
      <div class="control-label">
        <strong>
          <?php echo JText::_('COM_RENTAL_NOTE_FIELD_SUBJECT_LABEL'); ?>
        </strong>
      </div>
      <div class="controls">
        <?php echo $this->item->subject; ?>
      </div>
    </div>
    <div class="control-group">
      <div class="control-label">
        <strong>
          <?php echo JText::_('COM_RENTAL_NOTE_FIELD_MESSAGE_LABEL'); ?>
        </strong>
      </div>
      <div class="controls">
        <?php echo $this->item->body; ?>
      </div>
    </div>
      <input type="hidden" name="task" value="note.add" />
  <input type="hidden" name="property_id" value="<?php echo $this->item->property_id ?>" />
    <?php echo JHtml::_('form.token'); ?>
  </fieldset>
</form>