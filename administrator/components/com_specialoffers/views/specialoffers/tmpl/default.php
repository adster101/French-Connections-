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

// Check relevant permissions for this user
$canChangeState = $user->authorise('core.edit.state', 'com_specialoffers');
$canEditOwn = $user->authorise('core.edit.own', 'com_specialoffers');
$canEdit = $user->authorise('core.edit', 'com_specialoffers');
?>
<form action="<?php echo JRoute::_('index.php?option=com_specialoffers'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
      <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?> 
                 <?php echo $toolbar = JToolbar::getInstance('toolbar')->render('toolbar'); ?>

    <?php else : ?>
      <div id="j-main-container">
        <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
      <?php endif; ?>
      <?php if (count($this->items) == 0) : ?>
        <div class="alert alert-info">
          <?php echo JText::_('COM_SPECIALOFFERS_NO_OFFERS_FOUND'); ?>
        </div>
      <?php else: ?>
        <table class="table table-striped" id="articleList">
          <thead>
            <tr>
              <th class="hidden-phone">
                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
              </th>
              <th>
                <?php echo JText::_('JPUBLISHED'); ?>
              </th>    
              <th>
                <?php echo JHtml::_('grid.sort', 'COM_SPECIALOFFERS_OFFER_TITLE', 'so.title', $listDirn, $listOrder); ?>

              </th>
              <th>
                <?php echo JHtml::_('grid.sort', 'COM_SPECIALOFFERS_PROPERTY_ID', 'c.unit_title', $listDirn, $listOrder); ?>
              </th>

              <th>
                <?php echo JHtml::_('grid.sort', 'COM_SPECIALOFFERS_OFFER_START_DATE', 'a.start_date', $listDirn, $listOrder); ?>

              </th>
              <th>
                <?php echo JText::_('COM_SPECIALOFFERS_OFFER_END_DATE'); ?>
              </th>
            </tr>		
          </thead> 
          <tbody>

            <?php foreach ($this->items as $i => $item): ?>
              <tr class="row<?php echo $i % 2; ?>" sortable-group-id>
                <td class="hidden-phone">
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                  <?php echo JHtml::_('jgrid.published', $item->published, $i, 'specialoffers.', $canChangeState, 'cb', $item->start_date, $item->end_date); ?>
                  <?php if ($item->published && strtotime($item->start_date) < time() && strtotime($item->end_date) > time()) : // Offer is current?>
                    <?php echo JText::_('COM_SPECIALOFFERS_OFFER_STATUS_ACTIVE'); ?>
                  <?php elseif ($item->published && strtotime($item->end_date) < time()) : // Offer is published but expired?>  
                    <?php echo JText::_('COM_SPECIALOFFERS_OFFER_STATUS_EXPIRED'); ?>
                  <?php elseif ($item->published && strtotime($item->start_date) > time()) : // Offer is published but scheduled for future date ?>  
                    <?php echo JText::_('COM_SPECIALOFFERS_OFFER_STATUS_SCHEDULED'); ?>
                  <?php elseif (!$item->published) : // Offer is awaiting moderation ?>
                    <?php echo JText::_('COM_SPECIALOFFERS_OFFER_STATUS_AWAITING_APPROVAL'); ?>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($canEdit) : ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_specialoffers&task=specialoffer.edit&id=' . (int) $item->id); ?>">
                      <strong><?php echo JHtml::_('string.truncate', $this->escape(strip_tags($item->title)), 150); ?></strong>
                    </a><br />
                    <span class="small">
                      <?php echo $this->escape($item->description); ?>
                    </span>
                  <?php else: ?>
                    <p>
                      <strong>
                        <?php echo $this->escape($item->title); ?>
                      </strong>
                      <br />
                      <?php echo $this->escape($item->description); ?>
                    </p>

                  <?php endif; ?>
                </td>              
                <td>
                  <?php echo $this->escape($item->unit_title); ?> <span class="small">(<?php echo $item->listing_id; ?>)</span>
                </td>

                <td>
                  <?php echo JFactory::getDate($item->start_date)->calendar('d M Y'); ?>
                </td>
                <td>
                  <?php echo JFactory::getDate($item->end_date)->calendar('d M Y'); ?>
                </td>
              </tr>					
            <?php endforeach; ?>
          <input type="hidden" name="extension" value="<?php echo 'com_reviews'; ?>" />
          </tbody>
        </table>   
        <?php echo $this->pagination->getListFooter(); ?>

      <?php endif; ?>


      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

      <?php echo JHtml::_('form.token'); ?>

    </div>
</form>