<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canChange = $user->authorise('core.edit.state', 'com_featuredproperties');

$colspan = (isset($this->items[0])) ? count(get_object_vars($this->items[0])) : $colspan = 7;
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
        <div class="btn-group pull-left hidden-phone">
          <button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
          <button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value = '';
              this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
        </div>
      </div>
      <table class="table table-striped" id="articleList">
        <thead>
          <tr>
            <th width="1%" class="nowrap center hidden-phone">
              <?php echo JHtml::_('grid.checkall'); ?>
            </th>
            <th width="1%" class="hidden-phone">
              <?php echo JText::_('JGRID_HEADING_ID'); ?>
            </th>
            <th width="1%" class="nowrap center">
              <?php echo JText::_('Property Id'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_FEATUREDPROPERTY_PAID_STATUS'); ?>
            </th>
            <th class="title">
              <?php echo JHtml::_('grid.sort', 'Start date', 'a.start_date', $listDirn, $listOrder); ?>
            </th>
            <th class="title">
              <?php echo JHtml::_('grid.sort', 'End date', 'a.end_date', $listDirn, $listOrder); ?>
            </th>
            <th class="title">
              <?php echo JText::_('Featured on'); ?>
            </th>
            <th class="title">
              <?php echo JText::_('Notes'); ?>
            </th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <td colspan="<?php echo $colspan ?>">
              <?php echo $this->pagination->getListFooter(); ?>
            </td>
          </tr>
        </tfoot>
        <tbody>      
          <?php if (!empty($this->items)) : ?>
            <?php foreach ($this->items as $i => $item): ?>
              <tr class="row<?php echo $i % 2; ?>">
                <td>
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                  <?php echo $this->escape($item->id) ?>
                </td>
                <td class="">
                  <a href="<?php echo JRoute::_('index.php?option=com_featuredproperties&task=featuredproperty.edit&id=' . (int) $item->id); ?>">
                    <?php echo $this->escape($item->property_id); ?>
                  </a>
                </td>
                <td>
                  <?php echo JHtml::_('jgrid.state',  FeaturedPropertiesHelper::getPaidStates(), $item->published, $i, 'featuredproperties.', $canChange, true, 'cb') ?>

                </td>
                <td>
                  <?php echo $item->start_date ?>
                </td>
                <td>
                  <?php echo $item->end_date ?>
                </td>
                <td>
                  <?php echo $item->title ?>
                </td>
                <td>
                  <?php echo $item->notes ?>
                </td>
              </tr>					
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="<?php echo $colspan ?>">
                <p>No featured properties found. :-(</p>
              <td>
            </tr>
          <?php endif; ?>        



        </tbody>
      </table>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
    </div>
</form>