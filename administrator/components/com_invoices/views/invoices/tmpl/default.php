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
JHtml::_('behavior.formvalidation');

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
<script type="text/javascript">
  Joomla.orderTable = function()
  {
    table = document.getElementById("sortTable");
    direction = document.getElementById("directionTable");
    order = table.options[table.selectedIndex].value;
    if (order != '<?php echo $listOrder; ?>')
    {
      dirn = 'asc';
    }
    else
    {
      dirn = direction.options[direction.selectedIndex].value;
    }
    Joomla.tableOrdering(order, dirn, '');
  }
</script>
<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar))
{
  $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_invoices&view=invoices'); ?>" method="post" name="adminForm" id="adminForm">
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
          <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value = '';
    this.form.submit();"><i class="icon-remove"></i></button>
        </div>
        <div class="btn-group pull-right hidden-phone">
          <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
          <?php echo $this->pagination->getLimitBox(); ?>
        </div>
        <div class="btn-group pull-right hidden-phone">
          <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
          <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
            <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
            <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
            <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
          </select>
        </div>
        <div class="btn-group pull-right">
          <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
          <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
            <option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
            <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
          </select>
        </div>
      </div>
      <div class="clearfix"> </div>
      <?php echo JText::_('COM_INVOICES_INVOICES_LIST_BLURB'); ?>
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
              <th  class="nowrap center hidden-phone">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
              </th>
            <?php endif; ?>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_INVOICES_INVOICES_PROPERTY_ID', 'a.property_id', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_INVOICES_INVOICES_DATE_CREATED', 'a.date_created', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_INVOICES_INVOICES_TOTAL_NET', 'a.total_net', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_INVOICES_INVOICES_VAT', 'a.vat', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_INVOICES_INVOICES_DUE_DATE', 'a.due_date', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_INVOICES_INVOICES_FIRST_NAME', 'a.first_name', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
              <?php echo JHtml::_('grid.sort', 'COM_INVOICES_INVOICES_SURNAME', 'a.surname', $listDirn, $listOrder); ?>
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
            $canCreate = $user->authorise('core.create', 'com_invoices');
            $canEdit = $user->authorise('core.edit', 'com_invoices');
            $canCheckin = $user->authorise('core.manage', 'com_invoices');
            $canChange = $user->authorise('core.edit.state', 'com_invoices');
            ?>
            <tr class="row<?php echo $i % 2; ?>">
              <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>
              <?php if (isset($this->items[0]->state)): ?>
                <td class="center">
                  <?php echo JHtml::_('jgrid.published', $item->state, $i, 'invoices.', $canChange, 'cb'); ?>
                </td>
              <?php endif; ?>

              <?php if (isset($this->items[0]->id)): ?>
                <td class="">
                  <?php echo (int) $item->id; ?>
                  <br />
                  <?php if (!empty($item->due_date)): ?>
                    For Advertising on Internet site French Connections for 1 year commencing <?php echo $item->due_date; ?>
                  <?php else: ?>
                    Sundries
                  <?php endif; ?>
                  <a href="<?php echo JRoute::_('index.php?option=com_invoices&view=invoice&invoice_id=' . (int) $item->id) ?>">
                    <?php echo JText::_('COM_INVOICES_INVOICES_VIEW_DETAIL'); ?>
                  </a>
                </td>
              <?php endif; ?>
              <td>
                <?php echo $item->property_id; ?>
              </td>
              <td>
                <?php echo $item->date_created; ?>
              </td>
              <td>
                <?php echo $item->total_net; ?>
              </td>
              <td>
                <?php echo $item->vat; ?>
              </td>
              <td>
                <?php echo $item->due_date; ?>
              </td>
              <td>
                <?php echo $item->first_name; ?>
              </td>
              <td>
                <?php echo $item->surname; ?>
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
<?php
// Instantiate a new JLayoutFile instance and render the batch button
$layout = new JLayoutFile('frenchconnections.general.modal');
echo $layout->render(array('title' => 'COM_INVOICES_IMPORT_FROM_MYOB', 'id' => '', 'task' => 'invoices.import'));
?>

