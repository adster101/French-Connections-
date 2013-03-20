<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

$data = JApplication::getUserState('listing', '');

?>  
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=offers'); ?>" method="post" name="adminForm" id="adminForm">

  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <?php
      $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
      echo $layout->render($data);
      ?>  
      <table id="articleList" class="table table-striped">
        <thead>
          <tr>
            <th width="1%" class="nowrap center hidden-phone">
              <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
            </th>
            <th width="1%">
              <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
            </th>			
            <th width="10%">
              <?php echo JText::_('COM_HELLOWORLD_OFFERS_HEADING_GREETING'); ?>
            </th>

          </tr>
        </thead>
        <?php
        $listOrder = $this->escape($this->state->get('list.ordering'));
        $user = JFactory::getUser();
        $userId = $user->id;
        $groups = $user->getAuthorisedGroups();
        $ordering = ($listOrder == 'a.lft');
        $originalOrders = array();

        foreach ($this->items as $i => $item):
          ?>

          <tr class="row<?php echo $i % 2; ?>">
            <td>
              <span class="sortable-handler hasTooltip <?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
                <i class="icon-menu"></i>
              </span>
              <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
            </td>
            <td>
              <img src="<?php echo '/images/property/' . (int) $this->item->id . '/thumbs/' . $item->image_file_name; ?>" />
            </td>
            <td>
              <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=image.edit&id=' . (int) $item->id) ?>">
  <?php echo $this->escape($item->caption); ?>
              </a>
            </td>
          </tr>				

<?php endforeach; ?>

        <tr>
          <td colspan="7">
          </td>
        </tr>



        <input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />
        <input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />


      </table>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>
    </div>

</form>


