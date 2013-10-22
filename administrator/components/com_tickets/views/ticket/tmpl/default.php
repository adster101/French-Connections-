<?php
/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');

$user = JFactory::getUser();
$userId = $user->get('id');
?>

<form action="<?php echo JRoute::_('index.php?option=com_tickets&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="validate form-horizontal">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span7">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_TICKET_TICKET_FIELDSET'); ?></legend>
        <?php foreach ($this->form->getFieldset('ticket') as $field): ?>
          <div class="control-group">
            <?php echo $field->label; ?>
            <div class="controls">
              <?php echo $field->input; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </fieldset>     
    </div>
    <div class="span3">
      <h3>Notes</h3>
      <?php if (!empty($this->item->notes)) : ?>
        <div class="notes" style="max-height: 700px;overflow-y: auto">
          <?php 
            krsort($this->item->notes, 1); ?>
          <?php foreach ($this->item->notes as $note): ?>
            <p><strong><?php echo $note['date']; ?> - <?php echo $note['user'] ?></strong></p>
            <p><?php echo $note['description']; ?></p>
            <hr />
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    <input type="hidden" name="boxchecked" value="0" />

    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />

</form>

<script type="text/javascript">
  Joomla.submitbutton = function(task)
  {
    if (task == 'ticket.cancel' || document.formvalidator.isValid(document.id('adminForm')))
    {
      Joomla.submitform(task, document.getElementById('adminForm'));
    }
  }
</script>


