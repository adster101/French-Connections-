<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');

$listDirn = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));
$user = JFactory::getUser();
$userId = $user->get('id');
$saveOrder = $listOrder == 'a.ordering';
$disableClassName = '';
$disabledLabel = '';
if ($saveOrder) {
  $saveOrderingUrl = 'index.php?option=com_attributes&task=attributes.saveOrderAjax&tmpl=component';
  JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
<script type="text/javascript">
  Joomla.orderTable = function() {
    table = document.getElementById("sortTable");
    direction = document.getElementById("directionTable");
    order = table.options[table.selectedIndex].value;
    if (order != '<?php echo $listOrder; ?>') {
      dirn = 'asc';
    } else {
      dirn = direction.options[direction.selectedIndex].value;
    }
    Joomla.tableOrdering(order, dirn, '');
  }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_attributes'); ?>" method="post" name="adminForm" id="adminForm">
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
          <label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
          <input type="text" name="filter_search" 
                 id="filter_search" 
                 value="<?php echo $this->escape($this->state->get('filter.search')); ?>" 
                 title="<?php echo JText::_('COM_CATEGORIES_ITEMS_SEARCH_FILTER'); ?>" 
                 placeholder="<?php echo JText::_('COM_CATEGORIES_ITEMS_SEARCH_FILTER'); ?>" />        
        </div>
        <div class="btn-group pull-left hidden-phone">
          <button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
          <button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
        </div>
        <div class="btn-group pull-right hidden-phone">
          <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
          <?php echo $this->pagination->getLimitBox(); ?>

        </div>	
      </div>

      <table class="table table-striped" id="articleList">
        <thead>
          <tr>
            <th width="1%" class="nowrap center hidden-phone">
              <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>

            </th>
            <th width="1%" class="hidden-phone">
              <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>

            <th width="1%">
              <?php echo JText::_('JSTATUS'); ?>
            </th>
            <th>
              <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
            </th>
            <th width="1%">
              <?php echo JText::_('JGRID_HEADING_ID'); ?>
            </th>
          </tr>		

        </thead> 
        <tbody>
          <?php
          foreach ($this->items as $i => $item):
            $canChange = $user->authorise('core.edit.state', 'com_attributes.attribute' . $item->id);
            ?>
            <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->attribute_type_id ?>">
              <td>
                <?php
                if (!$saveOrder) :
                  $disabledLabel = JText::_('JORDERINGDISABLED');
                  $disableClassName = 'inactive tip-top';
                endif;
                ?>
                <span class="sortable-handler hasTooltip <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
                  <i class="icon-menu"></i>
                </span>
                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />  
              </td>
              <td>
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>
              <td>
                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'attributes.', $canChange, 'cb'); ?>
              </td>
              <td class="">
                <a href="<?php echo JRoute::_('index.php?option=com_attributes&task=attribute.edit&id=' . (int) $item->id); ?>">
                  <?php echo $this->escape($item->title); ?>
                </a>
                <div class="small"><?php echo $this->escape($item->attribute_type); ?></div>

              </td>
              <td>
                <?php echo $item->id; ?>
              </td>




            </tr>					
          <?php endforeach; ?>
        <input type="hidden" name="extension" value="<?php echo 'com_attributes'; ?>" />
        </tbody>


      </table>
      <?php echo $this->pagination->getListFooter(); ?>

      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

      <?php echo JHtml::_('form.token'); ?>

    </div>
</form>