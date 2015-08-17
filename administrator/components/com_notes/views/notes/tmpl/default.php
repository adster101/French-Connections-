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

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$canEdit = $user->authorise('core.edit', 'com_notes');
?>
<form action="<?php echo JRoute::_('index.php?option=com_notes'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <div id="filter-bar" class="btn-toolbar">
        <div class="filter-search btn-group pull-left">
          <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_USERS_SEARCH_IN_NOTE_TITLE'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_USERS_SEARCH_IN_NOTE_TITLE'); ?>" />
        </div>
        <div class="btn-group">
          <button class="btn tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
          <button class="btn tip" type="button" onclick="document.id('filter_search').value = '';
            this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
        </div>
        <div class="clearfix"> </div>
      </div>

      <table class="table table-striped">
        <thead>
          <tr>
            <th width="1%" class="nowrap center">
              <input type="checkbox" name="toggle" value="" class="checklist-toggle" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>
            <th>
              <?php echo JText::_('COM_NOTES_SUBJECT_HEADING'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_NOTES_DATE_CREATED', 'a.created_time', $listDirn, $listOrder); ?>
            </th>
            <th>
              <?php echo JHtml::_('grid.sort', 'PRN', 'a.id', $listDirn, $listOrder); ?>
            </th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <td colspan="15">
              <?php echo $this->pagination->getListFooter(); ?>
            </td>
          </tr>
        </tfoot>
        <tbody>
          <?php foreach ($this->items as $i => $item) : ?>
            <?php $canChange = $user->authorise('core.edit.state', 'com_notes'); ?>
            <tr>
              <td class=" checklist">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>
              <td>
                <a href="<?php echo JRoute::_('index.php?option=com_notes&task=note.edit&id=' . $item->id); ?>">
                  <?php if ($item->subject) : ?>
                    <?php echo $this->escape($item->subject); ?>
                  <?php else : ?>
                    <?php echo JHtml::_('string.truncate', $item->body, 25, true, false); ?>
                  <?php endif; ?>							
                </a>
              </td>
              <td>
                <?php echo $this->escape($item->created_on); ?>
              </td>
              <td>
                <?php echo (int) $item->property_id; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
