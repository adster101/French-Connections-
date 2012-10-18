<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$listOrder = $this->escape($this->state->get('list.ordering'));
$user = JFactory::getUser();
$userId = $user->get('id');
$groups = $user->getAuthorisedGroups();
$ordering = ($listOrder == 'a.lft');
$originalOrders = array();
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld'); ?>" method="post" name="adminForm" class="form-validate" id="adminForm">
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
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_ID'); ?>
            </th>
            <th width="2%">
              <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
            </th>			
            <th>
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_GREETING'); ?>
            </th>

            <th width="3%">
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_PUBLISHED'); ?>
            </th>	
            <th width="12%">
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_MODIFIED'); ?>
            </th>
            <th width="12%">
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_EXPIRY'); ?>
            </th>
            <th width="5%">
              <?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
            </th>
            <th width="15%">
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_CREATED_BY'); ?>
            </th>
          </tr>
        </thead>

        <tbody><?php
              foreach ($this->items as $i => $item):
                $orderkey = array_search($item->id, $this->ordering[$item->parent_id]);
                $canEditOwn = $user->authorise('core.edit.own', 'com_helloworld') && $item->created_by == $userId || in_array(8, $groups) || in_array(11, $groups);
                $canPublish = $user->authorise('helloworld.edit.publish', 'com_helloworld');
                $canReorder = $user->authorise('helloworld.edit.reorder', 'com_helloworld');
                ?>

            <?php if ($canEditOwn) : ?>
              <tr class="row<?php echo $i % 2; ?>">
                <td>		
                  <?php echo $item->id; ?>
                </td>
                <td>
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>

                <td>
                  <?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1) ?>
                  <a title="" href="<?php echo JRoute::_('index.php?option=com_helloworld&task=helloworld.edit&id=' . (int) $item->id) . '&' . JSession::getFormToken() . '=1'; ?>">
                    <?php echo $this->escape($item->greeting); ?>
                  </a>
                </td>

                <td class="center">
                  <?php echo JHtml::_('jgrid.published', $item->published, $i, 'helloworlds.', $canPublish); ?>
                </td>
                <td class="center">
                  <?php echo JText::_($item->modified); ?>
                </td>
                <td class="center">
                  <?php echo JText::_($item->expiry_date); ?>
                </td>
                <td class="order">
                  <?php if ($canReorder) : ?>
                    <span><?php
              if ($item->parent_id != 1) {
                echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'helloworlds.orderup', 'JLIB_HTML_MOVE_UP', $ordering);
              }
                    ?></span>
                    <span><?php
                if ($item->parent_id != 1) {
                  echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'helloworlds.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering);
                }
                    ?></span>

                  <?php else : ?>
                    <?php echo $orderkey + 1; ?>
                  <?php endif; ?>
                </td>		
                <td class="center">
                  <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>">
                    <?php echo JText::_($item->author_name); ?>
                  </a>
                </td>	

              </tr>					
            <?php else : ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />
        <input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />

        </tbody>
        <tfoot>
          <tr>
            <td colspan="7"></td>
          </tr>
        </tfoot>
      </table>
      <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
      </div>
    </div>
</form>
