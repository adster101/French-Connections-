<?php
/**
 * @version     1.0.0
 * @package     com_vouchers
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');

// Import CSS
$document = JFactory::getDocument();

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
  $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_tickets'); ?>" method="post" name="adminForm" id="adminForm" class="js-stools-form">

  <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>


      <table class="table table-striped" id="invoiceList">
        <thead>
          <tr>
            <?php if (isset($this->items[0]->ordering)): ?>
              <th width="1%" class="nowrap center hidden-phone">
                <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
              </th>
            <?php endif; ?>
            <th width="1%" class="hidden-phone">
              <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>
            <?php if (isset($this->items[0]->state)): ?>
              <th width="1%" class="nowrap center">
                <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
              </th>
            <?php endif; ?>
            <?php if (isset($this->items[0]->id)): ?>
              <th  class="nowrap  hidden-phone">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
              </th>
            <?php endif; ?>

            <th class='left'>
              <?php echo JText::_('JGLOBAL_TITLE'); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_TICKETS_SEVERITY', 'a.severity', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_TICKETS_AREA_SORT', 'a.area', $listDirn, $listOrder); ?>
            </th>
            <th>
              <?php echo JHtml::_('grid.sort', 'COM_TICKETS_ASSIGNED_TO', 'a.assigned_to', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_TICKETS_DATE_UPDATED', 'a.date_updated', $listDirn, $listOrder); ?>
            </th>
          </tr>
        </thead>
        <tfoot>
          <?php
          if (isset($this->items[0])) {
            $colspan = count(get_object_vars($this->items[0])) + 1;
          } else {
            $colspan = 10;
          }
          ?>
          <tr>
            <td colspan="<?php echo $colspan ?>">
              <?php echo $this->pagination->getListFooter(); ?>
            </td>
          </tr>
        </tfoot>
        <tbody>
          <?php
          foreach ($this->items as $i => $item) :
            $canChange = $user->authorise('core.edit.state', 'com_vouchers');
            $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
            ?>
            <tr class="row<?php echo $i % 2; ?>">
              <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>
              <?php if (isset($this->items[0]->state)): ?>
                <td>
                  <?php echo JHtml::_('tickets.state', $item->state, $i, $canChange, 'cb'); ?>
                </td>
              <?php endif; ?>

              <?php if (isset($this->items[0]->id)): ?>
                <td >
                  <?php echo (int) $item->id; ?>   

                </td>
              <?php endif; ?>
              <td>
                <?php if ($item->checked_out) : ?>
                  <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'tickets.', $canCheckin); ?>
                <?php endif; ?>
                <a rel="tooltip" title="<?php echo JHtml::_('string.truncate', $item->description, 250); ?>" href="<?php echo JRoute::_('index.php?option=com_tickets&task=ticket.edit&id=' . (int) $item->id) ?>">
                  <?php echo $this->escape($item->title); ?>
                </a>
              </td>
              <td>
                <?php echo $item->severity; ?>
              </td>
              <td>
                <?php echo $this->escape($item->area); ?>

              </td>
              <td>
                <?php echo $item->name; ?>
              </td>

              <td>
                <?php echo $item->date_updated; ?>
              </td>




            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php //Load the batch processing form.    ?>
      <?php echo $this->loadTemplate('batch'); ?>

      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
    </div>
</form>


