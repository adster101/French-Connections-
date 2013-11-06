<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//$listDirn = $this->escape($this->state->get('list.direction'));
//$listOrder = $this->escape($this->state->get('list.ordering'));
$user = JFactory::getUser();
$userId = $user->get('id');
?>
<form action="<?php echo JRoute::_('index.php?option=com_attributes&view=attributetypes'); ?>" method="post" name="adminForm" id="adminForm">
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
            <th width="1%" class="hidden-phone">
              <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>

            <th width="1%">
              <?php echo JText::_('JSTATUS'); ?>
            </th>
            <th width="">
              <?php echo JText::_('JGLOBAL_TITLE'); ?>
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
            <tr class="row<?php echo $i % 2; ?>">
              <td>
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>
              <td>
                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'attributetypes.', $canChange, 'cb'); ?>
              </td>
              <td class="">
                <a href="<?php echo JRoute::_('index.php?option=com_attributes&task=attributetype.edit&id=' . (int) $item->id); ?>">
                  <?php echo $this->escape($item->title); ?>
                </a>
              </td>
              <td>
                <?php echo $item->id; ?>
              </td>
            </tr>					
          <?php endforeach; ?>
        <input type="hidden" name="extension" value="<?php echo 'com_classification'; ?>" />
        </tbody>


      </table>
              <?php echo $this->pagination->getListFooter(); ?>

      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <?php echo JHtml::_('form.token'); ?>

    </div>
</form>