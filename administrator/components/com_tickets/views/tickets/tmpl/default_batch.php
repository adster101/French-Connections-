<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
?>
<div class="modal hide fade" id="collapseModal">
  <div class="modal-header">
    <button type="button" role="presentation" class="close" data-dismiss="modal">&#215;</button>
    <h3><?php echo JText::_('COM_TICKETS_BATCH_OPTIONS'); ?></h3>
  </div>
  <div class="modal-body">
    <?php if ($published >= 0) : ?>
      <div class="control-group">
        <div class="controls">
          <?php echo JHtml::_('batch.item', 'com_tickets'); ?>
        </div>
      </div>
      <hr />

    <?php endif; ?>
    <div class="control-group">
      <div class="controlls">
        <label>State</label>
        <select>
          <?php echo JHtml::_('select.options', TicketsHelper::getStateOptions(), 'value', 'text'); ?>
        </select>
      </div>
    </div>
    <hr />
    <div class="control-group">
      <div class="controlls">
        <label>Severity</label>
        <select>
          <?php echo JHtml::_('select.options', TicketsHelper::getSeverities(), 'value', 'text'); ?>
        </select>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn" type="button" onclick="document.id('batch-category-id').value = '';
        document.id('batch-access').value = '';
        document.id('batch-language-id').value = '';
        document.id('batch-tag-id)').value = ''" data-dismiss="modal">
              <?php echo JText::_('JCANCEL'); ?>
    </button>
    <button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('ticket.batch');">
      <?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
    </button>
  </div>
</div>
