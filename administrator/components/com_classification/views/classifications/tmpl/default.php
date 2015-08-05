<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');


$listOrder = $this->escape($this->state->get('list.ordering'));
$user = JFactory::getUser();
$userId = $user->get('id');
$ordering = ($listOrder == 'lft');
$originalOrders = array();
$extension = $this->escape($this->state->get('filter.extension'));
$canChange = $user->authorise('core.edit.state', 'com_classification');

$listDirn = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));
$listOrder = $this->escape($this->state->get('list.ordering'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_classification&view=classifications'); ?>" method="post" name="adminForm" id="adminForm">
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
          <th width="2%">
            <?php echo JText::_('COM_CLASSIFICATION_CLASSIFICATION_HEADING_ID'); ?>
          </th>
          <th width="2%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
          </th>			
          <th>
            <?php echo JText::_('COM_CLASSIFICATION_CLASSIFICATION_TITLE'); ?>
          </th>
          <th width="3%">
            <?php echo JText::_('COM_CLASSIFICATION_CLASSIFICATION_PUBLISHED'); ?>
          </th>	

          <th width="10%">
            <?php if ($canChange) : ?>
              <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.lft', $listDirn, $listOrder); ?>
              <?php if ($ordering) : ?>
                <?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'classifications.saveorder'); ?>
              <?php endif; ?>	
            <?php else : ?>
              <?php echo JText::_('JGRID_HEADING_ORDERING'); ?>

            <?php endif; ?>
          </th>
        </tr>		
        </thead>
        <tbody>
        <?php
        foreach ($this->items as $i => $item):
          $orderkey = array_search($item->id, $this->ordering[$item->parent_id]);
          $canChange = $user->authorise('core.edit.state', 'com_classification');
          ?>
          <tr class="row<?php echo $i % 2; ?>">
            <td>		
              <?php echo $item->id; ?>
            </td>
            <td>
              <?php echo JHtml::_('grid.id', $i, $item->id); ?>
            </td>

            <td class="">
              <?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1) ?>
              <a href="<?php echo JRoute::_('index.php?option=com_classification&task=classification.edit&id=' . (int) $item->id); ?>">
                <?php echo $this->escape($item->title); ?>
              </a>
            </td>
            <td class="center">
              <?php echo JHtml::_('jgrid.published', $item->published, $i, 'classifications.', $canChange); ?>
            </td>	

            <td class="order">                                

              <?php if ($canChange) : ?>
                <?php if ($ordering) : ?>
                  <span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'classifications.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                  <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'classifications.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                <?php endif; ?>

                <?php $disabled = $ordering ? '' : 'disabled="disabled"'; ?>
                <input type="hidden" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>" <?php echo $disabled ?> class="text-area-order" />
                <?php $originalOrders[] = $orderkey + 1; ?>
              <?php else : ?>
                <?php echo $orderkey + 1; ?>
              <?php endif; ?>		
            </td>		

          </tr>					
        <?php endforeach; ?>
        <input type="hidden" name="extension" value="<?php echo 'com_classification'; ?>" />
        <input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
      </tbody>
      <tfoot>

        <tr>
          <td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>    
      </table>
      </tfoot>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
