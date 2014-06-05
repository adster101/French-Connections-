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
$document->addStyleSheet('components/com_vouchers/assets/css/invoices.css');

$user = JFactory::getUser();
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<?php
//Joomla Component Creator code to allow adding non-select list filters
if (!empty($this->extra_sidebar))
{
  $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_vouchers'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span3">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span9">
      <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
      <?php //echo $toolbar = JToolbar::getInstance('toolbar')->render('toolbar'); ?>
    <?php else : ?>
      <div id="j-main-container">
        <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
      <?php endif; ?>
      <table class="table table-striped" id="invoiceList">
        <thead>
          <tr>
      
            <th width="1%" class="hidden-phone">
              <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>
              <th class="nowrap">
                <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
              </th>
              <th class="nowrap">
                <?php echo JText::_('Voucher description') ?>
              </th>

            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_VOUCHERS_PROPERTY_ID', 'a.property_id', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_VOUCHERS_VOUCHERS_QUANTITY_NET', 'a.quantity', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_VOUCHERS_VOUCHERS_END_DATE', 'a.end_date', $listDirn, $listOrder); ?>
            </th>
            <th  class="nowrap  hidden-phone">
              <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
            </th>
          </tr>
        </thead>
        <tfoot>
          <?php
          if (isset($this->items[0]))
          {
            $colspan = count(get_object_vars($this->items[0]));
          }
          else
          {
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
            $ordering = ($listOrder == 'a.ordering');
            $canCreate = $user->authorise('core.create', 'com_vouchers');
            $canEdit = $user->authorise('core.edit', 'com_vouchers');
            $canCheckin = $user->authorise('core.manage', 'com_vouchers');
            $canChange = $user->authorise('core.edit.state', 'com_vouchers');
            ?>
            <tr class="row<?php echo $i % 2; ?>">
              <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>
                <td>
                  <?php echo JHtml::_('vouchers.state', $item->state, $i, $canChange, 'cb'); ?>
                </td>
              <td>
                <a href="<?php echo JRoute::_('index.php?option=com_vouchers&task=voucher.edit&id=' . (int) $item->id) ?>">
                  <?php echo $this->escape($item->item_cost_id . ' - ' . $item->description) ?>
                </a>
              </td>
              <td>
                <?php echo $item->property_id; ?>
              </td>
              <td>
                <?php echo (int) $item->quantity; ?>
              </td>
              <td>
                <?php echo $item->end_date; ?>
              </td>
              <td>
                <?php echo (int) $item->id ?>
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


