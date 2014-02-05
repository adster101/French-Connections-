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

// Check relevant permissions for this user
$canChangeState = $user->authorise('core.edit.state', 'com_enquiries');
$canEditOwn = $user->authorise('core.edit.own', 'com_enquiries');
$canEdit = $user->authorise('core.edit', 'com_enquiries');
?>
<form action="<?php echo JRoute::_('index.php?option=com_enquiries'); ?>" method="post" name="adminForm" id="adminForm">

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
                 maxlength="50"
                 id="filter_search"
                 value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                 title="<?php echo JText::_('COM_ITEMS_SEARCH_FILTER'); ?>"
                 placeholder="<?php echo JText::_('COM_ITEMS_SEARCH_FILTER'); ?>" />
        </div>
        <div class="btn-group pull-left hidden-phone">
          <button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
          <button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value = '';
              this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
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
            <th>
              <?php echo JText::_('COM_ENQUIRIES_ENQUIRY_DETAILS'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_ENQUIRIES_ENQUIRY_PERIOD'); ?>
            </th>
            <th>
              <?php echo JHtml::_('grid.sort', 'COM_ENQUIRIES_ENQUIRY_DATE_CREATED', 'e.date_created', $listDirn, $listOrder); ?>
            </th>
            <th>
              <?php echo JText::_('Replied'); ?>
            </th>
            <th>
              <?php echo JHtml::_('grid.sort', 'COM_ENQUIRIES_PROPERTY_ID', 'e.property_id', $listDirn, $listOrder); ?>
            </th>
            

          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->items as $i => $item): ?>
            <tr>
              <td class="hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>
              <?php if ($canChangeState) : // If user can change state just show them un/publish buttons (e.g. admin) ?>
                <td>
                  <?php echo JHtml::_('enquiries.state', $item->state, $i, 'enquiries.', $canChangeState, 'cb'); ?>
                </td>

              <?php endif; ?>
              <td>
                <?php if ($canEdit || $canEditOwn) : ?>
                  <a href="<?php echo JRoute::_('index.php?option=com_enquiries&task=enquiry.edit&id=' . (int) $item->id); ?>">
                    <strong>
                      <?php echo JText::sprintf('COM_ENQUIRIES_ENQUIRY_TITLE_FIRST_LAST', $item->forename, $item->surname); ?>
                    </strong>
                  </a>
                <br />
                  <?php if (!empty($item->message)) : ?>
                    <span class="small">
                      <?php echo JHtml::_('string.truncate', $item->message, 150); ?>
                    </span>
                  <?php endif; ?>
                <?php else: ?>
                  <strong>
                    <?php echo JText::sprintf('COM_ENQUIRIES_ENQUIRY_TITLE', $item->forename, $item->surname); ?>
                  </strong>
                  <br />
                  <span class="small">
                    <?php echo JHtml::_('string.truncate', $item->message, 150); ?>
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <?php echo JText::sprintf('COM_ENQUIRIES_ENQUIRY_PERIOD_FROM_TO', JFactory::getDate($item->start_date)->calendar('d M Y'), JFactory::getDate($item->end_date)->calendar('d M Y')); ?>
              </td>
              <td>
                <?php echo JFactory::getDate($item->date_created)->calendar('d M Y'); ?>
              </td>
              <td>
                <?php if ($item->replied): ?>
                  <?php echo JText::sprintf('COM_ENQUIRIES_ENQUIRY_REPLY_SENT_ON', JFactory::getDate($item->date_replied)->calendar('d M Y')) ?>
                <?php endif; ?>
              </td>
              <td>
                <?php echo (int) $item->property_id; ?>
              </td>

            </tr>
          <?php endforeach; ?>
        <input type="hidden" name="extension" value="<?php echo 'com_enquiries'; ?>" />
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
