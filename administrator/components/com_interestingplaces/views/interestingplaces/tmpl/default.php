<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
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
<form action="<?php echo JRoute::_('index.php?option=com_interestingplaces'); ?>" method="post" name="adminForm" id="adminForm">
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
                 title="<?php echo JText::_('COM_INTERESTINGPLACES_ITEMS_SEARCH_FILTER'); ?>" 
                 placeholder="<?php echo JText::_('COM_INTERESTINGPLACES_ITEMS_SEARCH_FILTER'); ?>" />        
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
            <?php echo JText::_('COM_INTERESTINGPLACES_HEADING_ID'); ?>
          </th>
          <th width="2%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
          </th>			
          <th>
            <?php echo JText::_('COM_INTERESTINGPLACES_TITLE'); ?>
          </th>
          <th width="3%">
            <?php echo JText::_('COM_INTERESTINGPLACES_PUBLISHED'); ?>
          </th>	
        </tr>		
        </thead>
        <tbody>
        <?php
        foreach ($this->items as $i => $item):
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
              <a href="<?php echo JRoute::_('index.php?option=com_interestingplaces&task=interestingplace.edit&id=' . (int) $item->id); ?>">
                <?php echo $this->escape($item->title); ?>
              </a>
            </td>
            <td class="center">
              <?php echo JHtml::_('jgrid.published', $item->published, $i, 'interestingplaces.', $canChange); ?>
            </td>	



          </tr>					
        <?php endforeach; ?>
        <input type="hidden" name="extension" value="<?php echo 'com_interestingplaces'; ?>" />
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
