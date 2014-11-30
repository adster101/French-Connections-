<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
/* @var $this UsersViewNotes */

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');

$input = JFactory::getApplication()->input;
?>

<h1 class="page-header"><?php echo JText::sprintf('COM_NOTES_NOTES_FOR_PROPERTY', $this->id); ?></h1>

<form action="<?php echo JRoute::_('index.php?option=com_notes&tmpl=component&view=notes&layout=modal&property_id=' . (int) $input->get('property_id')); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">

  <button class="btn btn-primary">
    <i class="icon-plus-2"></i>
    <?php echo Jtext::_('COM_NOTES_NOTE_ADD') ?>
  </button>
  <hr />

  <ul class="list-striped">
    <?php if ($this->items) : ?>
    <?php foreach ($this->items as $item) : ?>
    <li>
      <?php if ($item->subject) : ?>
      <h4>
        <?php echo JText::_($this->escape($item->subject)); ?>  
        <small class="muted"><?php echo JHtml::date($item->created_on, 'D d M Y H:i'); ?></small>
      </h4>   
      <?php endif; ?>
      <?php if ($item->body): ?>
      <p>
        <?php echo JHtml::_('string.truncate', $item->body, 175, true); ?>
      </p>        
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="alert alert-info">
      <h4>
        <?php echo JText::_('COM_NOTES_NO_NOTES_FOUND') ?>
      </h4>
    </div>
    <?php endif; ?>  
  </ul>
  <?php echo $this->pagination->getListFooter(); ?>

  <input type="hidden" name="task" value="note.add" />
  <input type="hidden" name="property_id" value="<?php echo $this->id ?>" />
  <?php echo JHtml::_('form.token'); ?>

  <?php if ($this->pagination->total > 10) : ?>
  <hr />
  <button class="btn btn-primary">
    <?php echo Jtext::_('COM_NOTES_NOTE_ADD') ?>
  </button>
  <?php endif; ?>
</form>
