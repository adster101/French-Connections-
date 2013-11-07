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
$saveOrder = $listOrder == 'r.ordering';
$disableClassName = '';
$disabledLabel = '';
$input = JFactory::getApplication()->input;

// Get the following options from the request.
$option = $input->get('option', '', 'string');
$view = $input->get('view', 'default', 'string');
$unit_id = $input->get('unit_id', '0', 'int');

// If the review is being viewed through the reviews view of hellowolrd then we need to redirect back there
// and not the to the com_reviews component.
$route = ($option == 'com_helloworld') ? $option . '&view=reviews&unit_id=' . (int) $unit_id : $option;

// Check relevant permissions for the user against the reviews component
$canDo = ReviewsHelper::getActions('com_reviews');

$canChangeState = ($option == 'com_helloworld') ? false : $canDo->get('core.edit.state');
$canEditOwn = $canDo->get('core.edit.own');
$canEdit = $canDo->get('core.edit');

// Set the data array (for the progress layout) based on the component we are in
$data = ($option == 'com_helloworld') ? array('item' => $this->unit, 'progress' => $this->progress) : array();
?>

<form action="<?php echo JRoute::_('index.php?option=' . $route); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
        <?php if ($option == 'com_reviews') : ?>
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
              <button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value = '';
                      this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
              <label for="limit" class="element-invisible">
                <?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
              <?php echo $this->pagination->getLimitBox(); ?>
            </div>
          </div>
        <?php endif; ?>
      <?php if (count($this->items) > 0) : ?> 

        <table class="table table-striped" id="articleList">
          <thead>
            <tr>
              <th width="1%" class="hidden-phone">
                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
              </th>
              <th>
                <?php echo JText::_('COM_REVIEWS_PROPERTY_ID'); ?>

              </th>
              <th>
                <?php echo JText::_('COM_REVIEWS_REVIEW_TEXT'); ?>
              </th>
              <th width="15%">
                <?php echo JText::_('COM_REVIEWS_REVIEWER'); ?>
              </th>
              <th width="15%">
                <?php echo JText::_('COM_REVIEWS_DATE_STATYED_AT_PROPERTY'); ?>
              </th>
              <th>
                <?php echo JText::_('JSTATUS'); ?>
              </th>


              <th>
                <?php echo JText::_('JGRID_HEADING_ID'); ?>
              </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($this->items as $i => $item): ?>
              <tr>
                <td>
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                  <?php echo $item->unit_title; ?>
                  (<?php echo $item->unit_id; ?>)
                </td>
                <td>
                  <?php echo JHtml::_('string.truncate', $item->review_text,0,true,false); // Don't allow html here. ?>
                  <?php if ($canEdit) : ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_reviews&task=review.edit&id=' . (int) $item->id); ?>">
                      <?php echo JText::_('JTOOLBAR_EDIT'); ?>
                    </a>
                  <?php endif; ?>  
                </td>
                <td>
                  <?php echo $this->escape($item->guest_name); ?>
                </td>
                <td>
                  <?php echo JFactory::getDate($item->date)->calendar('d M Y'); ?>
                </td>
                <td>
                  <?php echo JHtml::_('jgrid.published', $item->published, $i, 'reviews.', $canChangeState, 'cb'); ?>
                </td>
                <td>
                  <?php echo $item->id; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <input type="hidden" name="extension" value="<?php echo 'com_reviews'; ?>" />
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
<?php else: ?>
  <div class="alert alert-notice">
    <h4>No Reviews</h4>
    <p>No review were found against your account.</p>
  </div>

<?php endif; ?>
