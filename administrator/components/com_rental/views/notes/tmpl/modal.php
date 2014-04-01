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
?>

<h1><?php echo JText::sprintf('COM_RENTAL_NOTES_FOR_PROPERTY', $this->id); ?></h1>
<form class="form-horizontal" action="<?php echo JRoute::_('index.php?option=com_rental&view=notes&layout=modal&tmpl=component&property_id=' . (int) $this->id); ?>" method="post" name="adminForm" id="adminForm">

  <ul class="list-striped">
    <?php foreach ($this->items as $item) : ?>
      <li>
        <?php if ($item->subject) : ?>

          <a href="<?php echo JRoute::_('index.php?option=com_rental&view=notes&layout=modal&tmpl=component&id=' . $item->id) ?>">
            <strong><?php echo JText::_($this->escape($item->subject)); ?></strong>
          </a>
          <?php echo JHtml::date($item->created_time, 'D d M Y H:i'); ?>&ndash;
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php echo $this->pagination->getListFooter(); ?>

  <div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>
