<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canChange = $user->authorise('core.edit.state', 'com_featuredproperties');
$colspan = (isset($this->items[0])) ? count(get_object_vars($this->items[0])) + 1 : $colspan = 7;
?>

<form action="<?php echo JRoute::_('index.php?option=com_featuredproperties'); ?>" method="post" name="adminForm" id="adminForm" class=""> 

  <?php if (!empty($this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10"> 
      <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
    <?php else : ?>
      <div id="j-main-container">
        <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
      <?php endif; ?>
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
              <?php echo JText::_('PRN'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_FEATUREDPROPERTY_PAID_STATUS'); ?>
            </th>
            <th class="title">
              <?php echo JHtml::_('grid.sort', 'Start date', 'a.start_date', $listDirn, $listOrder); ?>
            </th>
            <th class="title">
              <?php echo JText::_('End date'); ?>
            </th>
            <th class="title">
              <?php echo JText::_('Featured on'); ?>
            </th>
            <th class="title">
              <?php echo JText::_('Notes'); ?>
            </th>
          </tr>
        </thead>

        <tbody>      
          <?php if (!empty($this->items)) : ?>
            <?php foreach ($this->items as $i => $item): ?>
              <tr>
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
                  <?php echo JHtml::_('jgrid.state', FeaturedPropertiesHelper::getPaidStates(), $item->published, $i, 'featuredproperties.', $canChange, true, 'cb') ?>
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
        <tfoot>
          <tr>
            <td colspan="<?php echo $colspan ?>">
              <?php echo $this->pagination->getListFooter(); ?>
            </td>
          </tr>
        </tfoot>
      </table>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
    </div>
</form>