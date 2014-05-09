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
  <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>


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
              <?php echo JText::_('COM_ENQUIRIES_ENQUIRY_FROM'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_ENQUIRIES_ENQUIRY_DETAILS'); ?>
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
                <?php else: ?>
                  <strong>
                    <?php echo JText::sprintf('COM_ENQUIRIES_ENQUIRY_TITLE', $item->forename, $item->surname); ?>
                  </strong>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($item->message)) : ?>
                  <?php echo JHtml::_('string.truncate', $item->message, 150); ?>
                <?php endif; ?>                  
                <p class="small">
                  (<?php echo JText::sprintf('COM_ENQUIRIES_FROM', $item->start_date); ?>
                  <?php echo JText::sprintf('COM_ENQUIRIES_TO', $item->end_date); ?>)
                </p>

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
                <?php echo $this->escape($item->unit_title); ?>
                (<?php echo (int) $item->property_id; ?>)
              </td>

            </tr>
          <?php endforeach; ?>

        <input type="hidden" name="extension" value="<?php echo 'com_enquiries'; ?>" />
        </tbody>
      </table>
      <?php echo $this->pagination->getListFooter(); ?>
      <?php if (!count($this->items) && $this->activeFilters) : ?>
        <div class="alert alert-info">
          <h4>No enquiries found for this search</h4>
          <p>No enquiries were found matching the filters you have applied. Please try again.</p>
        </div>
      <?php endif; ?>
      <?php if (!count($this->items) && !$this->activeFilters) : ?>
        <div class="alert alert-info">
          <h4>No enquiries</h4>
          <p>There are no enquiries listed against your property listings.</p>
        </div>
      <?php endif; ?>        

      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

      <?php echo JHtml::_('form.token'); ?>

    </div>
</form>
