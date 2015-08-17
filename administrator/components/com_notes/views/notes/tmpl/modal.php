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
JHtml::_('bootstrap.tooltip');

$input = JFactory::getApplication()->input;

$toolbar = JToolbar::getInstance('note');

      $toolbar->appendButton('Standard', 'plus', 'COM_NOTES_NOTE_ADD', 'note.add', false);
?>

<h1 class="page-header"><?php echo JText::sprintf('COM_NOTES_NOTES_FOR_PROPERTY', $this->id); ?></h1>

<form action="<?php echo JRoute::_('index.php?option=com_notes&tmpl=component&view=notes&layout=modal&property_id=' . (int) $input->get('property_id')); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">

  <?php echo $toolbar->render(); ?>
  
  <ul class="list-striped">
    <?php if ($this->items) : ?>
      <?php foreach ($this->items as $item) : ?>
        <li>
          <?php if ($item->subject) : ?>
            <h4>
              <?php echo JText::_($this->escape($item->subject)); ?>  
              <?php if ($item->body): ?>
                <a data-toggle="tooltip" title="<?php echo JHtml::_('string.truncate', $item->body, 300, true, false); ?>" href="<?php echo JRoute::_('index.php?option=com_notes&tmpl=component&view=note&layout=full&id=' . (int) $item->id) ?>">
                  <?php echo JText::_('COM_NOTES_SHOW_FULL_NOTE'); ?>
                </a>
              <?php endif; ?>
              <small class="muted"><?php echo JHtml::date($item->created_on, 'D d M Y H:i'); ?></small>
            </h4>   
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

  <input type="hidden" name="task" value="" />
  <input type="hidden" name="property_id" value="<?php echo $this->id ?>" />
  <?php echo JHtml::_('form.token'); ?>

</form>
