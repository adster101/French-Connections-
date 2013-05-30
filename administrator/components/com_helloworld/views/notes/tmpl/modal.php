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
JHtml::_('formbehavior.chosen', 'select');
$category = $this->escape($this->state->get('filter.category_id'));
?>
<h1><?php echo JText::sprintf('COM_HELLOWORLD_NOTES_FOR_PROPERTY', $this->id); ?></h1>
<form class="form-horizontal" action="<?php echo JRoute::_('index.php?option=com_helloworld&view=notes&layout=modal&tmpl=component&property_id=' . (int) $this->id); ?>" method="post" name="adminForm" id="adminForm">
  <fieldset class="filter">
    <div id="filter-bar" class="btn-toolbar">
      <div class="btn-group hidden-phone">
        <div class="control-group">
          <label for="filter_category_id" class="control-label"><?php echo JText::_('COM_HELLOWORLD_NOTES_FILTER'); ?></label>
          <div class="controls">
            <select name="filter_category_id" id="filter_category_id" class="pull-right input-xxlarge" onchange="this.form.submit()">
              <option value=""></option>
              <?php echo JHtml::_('select.options', JHtml::_('category.categories', 'com_helloworld'), 'value', 'text', $category); ?>
            </select>
          </div>
        </div>
      </div>
    </div>
  </fieldset>
  <ul class="alternating">
    <?php foreach ($this->items as $item) : ?>
      <li>
          <?php if ($item->subject) : ?>
            <h4>
              <?php echo JHtml::date($item->created_time, 'D d M Y H:i'); ?>&ndash;
              <?php echo JText::_($this->escape($item->subject)); ?></h4>
          <?php else : ?>
            <h4>
              <?php echo JHtml::date($item->created_time, 'D d M Y H:i'); ?>&ndash;
              <?php echo JText::sprintf('COM_HELLOWORLD_NOTES_SUBJECT', (int) $item->id, JText::_('COM_USERS_EMPTY_SUBJECT')); ?>
            </h4>
          <?php endif; ?>

        <div class="ubody">
          <?php echo $item->body; ?>
        </div>
        <hr />
      </li>
    <?php endforeach; ?>
  </ul>
  <?php echo $this->pagination->getListFooter(); ?>

  <div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>
