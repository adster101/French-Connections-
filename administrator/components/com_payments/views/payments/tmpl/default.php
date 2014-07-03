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
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_invoices/assets/css/invoices.css');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canOrder = $user->authorise('core.edit.state', 'com_invoices');
$saveOrder = $listOrder == 'a.ordering';

$sortFields = $this->getSortFields();

?>

<form action="<?php echo JRoute::_('index.php?option=com_payments&view=payments'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>

      <div id="filter-bar" class="btn-toolbar">
        <div class="filter-search btn-group pull-left">
          <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
          <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
        </div>
        <div class="btn-group pull-left">
          <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
          <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
        </div>
        <div class="btn-group pull-right hidden-phone">
          <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
          <?php echo $this->pagination->getLimitBox(); ?>
        </div>

      </div>

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

              <th  class="nowrap hidden-phone">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
              </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_PAYMENTS_PAYMENTS_PROPERTY_ID', 'a.property_id', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_PAYMENTS_PAYMENTS_DATE_CREATED', 'a.DateCreated', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_PAYMENTS_PAYMENTS_TOTAL_NET', 'a.amount', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_PAYMENTS_PAYMENTS_FIRST_NAME', 'a.InvoiceName', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_PAYMENTS_PAYMENTS_SURNAME', 'a.surname', $listDirn, $listOrder); ?>
            </th>
          </tr>
        </thead>
        <tfoot>
          <?php
          if (isset($this->items[0])) {
            $colspan = count(get_object_vars($this->items[0]));
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
            $ordering = ($listOrder == 'a.ordering');
            $canCreate = $user->authorise('core.create', 'com_invoices');
            $canEdit = $user->authorise('core.edit', 'com_invoices');
            $canCheckin = $user->authorise('core.manage', 'com_invoices');
            $canChange = $user->authorise('core.edit.state', 'com_invoices');
            ?>
            <tr class="row<?php echo $i % 2; ?>">
              <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>

                <td class="">
                  <a href="<?php echo JRoute::_('index.php?option=com_payments&view=payment&id=' . (int) $item->id) ?>">
                    <?php echo $item->VendorTxCode; ?>
                  </a>
                </td>
              <td>
                <?php echo $item->property_id; ?>
              </td>
              <td>
                <?php echo JFactory::getDate($item->DateCreated)->calendar('d-m-Y'); ?>
              </td>
              <td>
                <?php echo $item->Amount; ?>
              </td>
              <td>
                <?php echo $item->name; ?>
              </td>
              <td>
                <?php echo $item->user_id; ?>
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


