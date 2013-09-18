<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<form action="<?php echo JRoute::_('index.php?option=com_featuredproperties'); ?>" method="post" name="adminForm" id="adminForm">
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
          <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE'); ?></label>
          <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_MODULES_MODULES_FILTER_SEARCH_DESC'); ?>" />
        </div>
      </div>
    <table class="table table-striped" id="articleList">
      <thead>
        <tr>
          <th width="1%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
          </th>
          <th width="1%" class="hidden-phone">
            <?php echo JHtml::_('grid.checkall'); ?>
          </th>
          <th width="1%" class="nowrap center">
            <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
          </th>
          <th class="title">
            <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="10">
            <?php echo $this->pagination->getListFooter(); ?>
          </td>
        </tr>
      </tfoot>
      <tbody>      
        

        
        
      </tbody>
    </table>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>