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

<form action="<?php echo JRoute::_('index.php?option=com_vouchers'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span3">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span9">
      <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
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
              <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
            </th>
            <th class="nowrap">
              <?php echo JText::_('Voucher description') ?>
            </th>

            <th class='left'>
              <?php echo JHtml::_('searchtools.sort', 'COM_VOUCHERS_PROPERTY_ID', 'a.property_id', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
              <?php echo JText::_('COM_VOUCHERS_VOUCHERS_QUANTITY_NET'); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('searchtools.sort', 'COM_VOUCHERS_VOUCHERS_END_DATE', 'a.end_date', $listDirn, $listOrder); ?>
            </th>
            <th  class="nowrap  hidden-phone">
              <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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

            $canChange = $user->authorise('core.edit.state', 'com_vouchers');
            ?>
            <tr class="row<?php echo $i % 2; ?>">
              <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>
              <td>
                <?php echo JHtml::_('jgrid.published', $item->state, $i, 'vouchers.', $canChange, 'cb', $item->date_created, $item->end_date); ?>
                <?php // echo JHtml::_('vouchers.state', $item->state, $i, $canChange, 'cb'); ?>
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

      <?php echo JHtml::_('form.token'); ?>
    </div>
</form>


