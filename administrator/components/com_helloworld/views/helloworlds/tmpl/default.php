<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('dropdown.init');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$listDirn = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));
$user = JFactory::getUser();
$userId = $user->get('id');
$groups = $user->getAuthorisedGroups();
$ordering = ($listOrder == 'a.lft');
$originalOrders = array();

$canDo = HelloWorldHelper::getActions();
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
            <th width="5%">
              <?php if ($canDo->get('helloworld.sort-by-prn')) : ?>
                <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_HELLOWORLD_HEADING_ID', 'id', $listDirn, $listOrder); ?>
              <?php else : ?>
                <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_ID') ?>
              <?php endif; ?>
            </th>
            <th width="2%">
              <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>			
            <th>
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_GREETING'); ?>
            </th>

            <th width="12%">
              <?php if ($canDo->get('helloworld.sort-by-expiry')) : ?>
                <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_EXPIRY', 'expiry_date', $listDirn, $listOrder); ?>
              <?php else: ?>
                <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_EXPIRY'); ?>
              <?php endif; ?>
            </th>
            <th width="5%">
              <?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
            </th>
            <?php if ($canDo->get('helloworld.display-owner')) : ?>
              <th>
                <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_CREATED_BY'); ?>
              </th>
            <?php endif; ?>
            <th width="3%">
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_PUBLISHED'); ?>
            </th>	
            <th>              
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_MODIFIED'); ?>
            </th>
          </tr>
        </thead>

        <tbody><?php
              foreach ($this->items as $i => $item):
                $orderkey = array_search($item->id, $this->ordering[$item->parent_id]);
                $canEditOwn = $user->authorise('core.edit.own', 'com_helloworld') && $item->created_by == $userId || in_array(8, $groups) || in_array(11, $groups);
                $canPublish = $user->authorise('helloworld.edit.publish', 'com_helloworld');
                $canReorder = $user->authorise('helloworld.edit.reorder', 'com_helloworld');
                $expiry_date = new DateTime($item->expiry_date);
                $now = date('Y-m-d');
                $now = new DateTime($now);
                
                $days_to_renewal = $now->diff($expiry_date)->format('%R%a');
                ?>

            <?php if ($canEditOwn) : ?>
              <tr class="row<?php echo $i % 2; ?>">
                <td>		
                  <?php echo ($item->parent_id != 1) ? '<span class="small muted">' . $item->id . '</span>' : $item->id; ?>
                </td>
                <td>
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>

                <td>
                  <?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1) ?>
                  <?php if ($item->parent_id == 1) : ?>
                    <img width="30px" src=<?php echo '/images/property/' . $item->id . '/thumbs/' . $item->thumbnail ?> />
                  <?php endif; ?>
                  <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=helloworld.edit&id=' . (int) $item->id) . '&' . JSession::getFormToken() . '=1'; ?>">
                    <?php echo $this->escape($item->title); ?>
                  </a>

                </td>

                <td>                  
                  <?php echo ($item->parent_id == 1) ? JText::_($item->expiry_date) : ''; ?>
                  
                  <?php if ( $days_to_renewal > 0 && $item->parent_id == 1 && $days_to_renewal < 28) : ?>
                    <br /><span><?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_DAYS_TO_RENEWAL',$days_to_renewal); ?></span>
                  <?php elseif ($item->parent_id == 1 ) : ?>
                    <a class="btn btn-danger btn-small">
                      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_RENEW_NOW'); ?>
                    </a>
                    
                  <?php endif; ?>
                  
                </td>
                <td class="order">
                  <?php if ($canReorder) : ?>
                    <?php if ($item->parent_id != 1) : ?>
                      <span>
                        <?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'helloworlds.orderup', 'JLIB_HTML_MOVE_UP', $canReorder); ?>
                      </span>
                    <?php endif; ?>
                    <?php if ($item->parent_id != 1) : ?>
                      <span>
                        <?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'helloworlds.orderdown', 'JLIB_HTML_MOVE_DOWN', $canReorder); ?>                     
                      </span>
                    <?php endif; ?>
                  <?php else : ?>
                    <?php echo $orderkey + 1; ?>
                  <?php endif; ?>
                </td>
                <?php if ($canDo->get('helloworld.display-owner')) : ?>

                  <td>
                    <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>">
                      <?php echo JText::_($item->author_name); ?>
                    </a>
                  </td>	
                <?php endif; ?>
                <td class="center">
                  <?php echo JHtml::_('jgrid.published', $item->published, $i, 'helloworlds.', $canPublish); ?>
                </td>                  
                <td>
                  <?php echo JText::_($item->modified); ?>
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
      <?php echo $this->pagination->getListFooter(); ?>

      <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
      </div>
    </div>
</form>

<?php //echo $this->loadTemplate('new');   ?>

