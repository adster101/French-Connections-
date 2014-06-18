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
<form action="<?php echo JRoute::_('index.php?option=com_rental&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
  <fieldset class="filter">
    <div id="filter-bar" class="btn-toolbar ">

      <button class="btn btn-primary">
        <i class="icon icon-plus-2"></i>&nbsp;Add note</button>

    </div>
  </fieldset>
  <ul class="list-striped">
    <?php if ($this->items) : ?>
      <?php foreach ($this->items as $item) : ?>
        <li>
          <?php if ($item->subject) : ?>
            <h4>
              <?php echo JText::_($this->escape($item->subject)); ?>  
              <small class="muted"><?php echo JHtml::date($item->created_time, 'D d M Y H:i'); ?></small>
            </h4>   
          <?php endif; ?>
          <?php if ($item->body): ?>
            <p>
              <?php echo JHtml::_('string.truncate', $item->body, 75, true); ?>

            </p>        
            <?php if (strlen($item->body) > 75) : ?> 
              <a href="<?php echo JRoute::_('index.php?option=com_rental&view=note&layout=default&tmpl=component&id=' . $item->id) ?>">
                <?php echo JText::_('COM_RENTAL_VIEW_NOTE_DETAIL'); ?>  
              </a> 
            <?php endif; ?>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="alert alert-info">
        <h4>
          <?php echo JText::_('COM_RENTAL_NO_NOTES_FOUND') ?>
        </h4>
      </div>
    <?php endif; ?>  
  </ul>
  <input type="hidden" name="task" value="note.add" />
  <input type="hidden" name="property_id" value="<?php echo $this->id ?>" />
  <?php echo JHtml::_('form.token'); ?>
  <div>
  </div>
</form>
