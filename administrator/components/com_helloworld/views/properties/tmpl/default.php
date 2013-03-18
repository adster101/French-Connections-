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

$listing_id = '';
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
                 placeholder="<?php echo JText::_('COM_HELLOWORLD_PROPERTY_SEARCH_FILTER'); ?>" />
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
      <?php if (empty($this->items)) : // This user doesn't have any listings against their account ?>
        <hr />
        <div class="alert alert-block">
          <strong><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_NO_LISTINGS'); ?><strong>
        </div>
        <hr/>
      <?php else: ?>
      <table class="table table-striped" id="articleList">
        <thead>
          <tr>
            <th width="5%">
              <?php if ($canDo->get('helloworld.sort.prn')) : ?>
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
              <?php if ($canDo->get('helloworld.sort.expiry')) : ?>
                <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_EXPIRY', 'expiry_date', $listDirn, $listOrder); ?>
              <?php else: ?>
                <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_EXPIRY'); ?>
              <?php endif; ?>
            </th>
            <th>
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_RENEWAL'); ?>
            </th>

            <?php if ($canDo->get('helloworld.display.owner')) : ?>
              <th>
                <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_CREATED_BY'); ?>
              </th>
            <?php endif; ?>
            <th>
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_PUBLISHED'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_RECENT_PAGE_VIEWS'); ?>
            </th>

            <th>
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_MODIFIED'); ?>
            </th>
            <?php if ($canDo->get('helloworld.snooze')) : ?>
              <th>
                <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_SNOOZE'); ?>
              </th>
            <?php endif; ?>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($this->items as $i => $item): ?>
            <?php
            //$orderkey = array_search($item->id, $this->ordering[$item->parent_id]);
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
                  <?php echo $item->id; ?>
                </td>
                <td>
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>

                <td>
                  <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=property.edit&id=' . (int) $item->id) . '&' . JSession::getFormToken() . '=1'; ?>">
                    <?php echo $this->escape($item->title); ?>
                  </a>
                </td>
                <td>
                  <?php echo $item->expiry_date; ?>



                </td>
                <td>
                  <?php if ($days_to_renewal < 28 && $days_to_renewal > 0) : ?>
                    <span><?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_DAYS_TO_RENEWAL', $days_to_renewal); ?></span>
                    <br />
                    <a class="btn btn-danger btn-small">
                      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_RENEW_NOW'); ?>
                    </a>
                  <?php elseif ($days_to_renewal < 0) : ?>
                    <a class="btn btn-danger btn-small">
                      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_RENEW_NOW'); ?>
                    </a>
                  <?php elseif (empty($item->expiry_date)): ?>
                    &mdash;
                  <?php elseif ($days_to_renewal > 28) : ?>
                    <?php echo JHtml::_('autorenew.state', $item->auto_renew, $i, 'enquiries.', 1, 'cb'); ?>

                  <?php endif; ?>
                </td>

                <?php if ($canDo->get('helloworld.display.owner')) : ?>

                  <td>
                    <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>">
                      <?php echo JText::_($item->name); ?>
                    </a>
                    <br />
                    <span class="small muted">
                      <?php echo JText::_($item->email); ?>
                      <br />
                      <?php echo JText::_($item->phone_1); ?>
                    </span>
                  </td>
                <?php endif; ?>
                <td class="center">
                  <?php echo JHtml::_('jgrid.published', $item->published, $i, 'properties.', $canPublish); ?>
                </td>
                <td>
                  <?php echo JText::_($item->count); ?>
                </td>
                <td>
                  <?php echo JText::_($item->modified); ?>
                </td>
                <?php if ($canDo->get('helloworld.snooze')) : ?>
                  <td>
                    <a class="btn btn-micro active" href="<?php echo JRoute::_('index.php?option=com_helloworld&task=snooze.update&id=' . $item->id . '&' . JSession::getFormToken() . '=1') ?>">
                      <i class="icon-calendar"></i>
                    </a>
                  </td>
                <?php endif; ?>
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
      <?php endif; ?>

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

<?php
$layout = new JLayoutFile('modal', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
echo $layout->render($data = array());
?>
