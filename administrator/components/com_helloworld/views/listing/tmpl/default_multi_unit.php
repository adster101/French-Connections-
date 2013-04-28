<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('dropdown.init');


$listDirn = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));
$expiry_start_date = $this->state->get('filter.expiry_start_date');
$expiry_end_date = $this->state->get('filter.expiry_end_date');

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
        <div class="btn-group pull-right hidden-phone">
          <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
          <?php echo $this->pagination->getLimitBox(); ?>
        </div>
      </div>
      <?php if (empty($this->items)) : // This listings doesn't have any listings against their account ?>
        <hr />
        <div class="alert alert-block">
          <strong><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_NO_LISTINGS'); ?><strong>
              </div>
              <hr/>
            <?php elseif (count($this->items) == 1) : ?> 
              <?php $this->loadTemplate('single_unit'); ?>
            <?php else: ?>
              <table class="table table-striped" id="articleList">
                <thead>
                  <tr>
                    <th>
                      <?php if ($canDo->get('helloworld.sort.prn')) : ?>
                        <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_HELLOWORLD_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                      <?php else : ?>
                        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_ID') ?>
                      <?php endif; ?>
                    </th>
                    <th width="2%">
                      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <?php if ($canDo->get('core.edit.state')) : ?>
                      <th>
                        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_ACTIVE'); ?>
                      </th>
                    <?php endif; ?>
                    <th>
                      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_GREETING'); ?>
                    </th>
                    <th>
                      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_MODIFIED'); ?>
                    </th>
                  </tr>
                </thead>
                <?php
                $canEditOwn = $canDo->get('core.edit.own');
                $canPublish = $canDo->get('helloworld.edit.publish');

                $canSubmitForReview = $canDo->get('helloworld.property.submit');
                $canReview = $canDo->get('helloworld.property.review');
                ?>

                <tbody>
                  <?php foreach ($this->items as $i => $item): ?>
                    <?php
                    $expiry_date = new DateTime($item->expiry_date);
                    $now = date('Y-m-d');
                    $now = new DateTime($now);
                    $days_to_renewal = $now->diff($expiry_date)->format('%R%a');

                    if ($item->review == 0) {
                      $enabled = false;
                    } elseif ($item->review == 1) {
                      $enabled = $canDo->get('helloworld.property.submit');
                    } elseif ($item->review == 2) {
                      $enabled = $canDo->get('helloworld.property.review');
                    } elseif ($item->review == -1) {
                      $enabled = false;
                    }
                    ?>
                    <?php if ($canEditOwn) : ?>
                      <tr class="row<?php echo $i % 2; ?>">
                        <td>
                          <?php echo $item->id; ?>
                        </td>
                        <td>
                          <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <?php if ($canDo->get('core.edit.state')) : ?>
                          <td>
                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'properties.', $canPublish, 'cb', $item->created_on, $item->expiry_date); ?>
                          </td>
                        <?php endif; ?>
                        <td>
                          <?php if ($item->review != 2) : ?>
                            <!-- 
                              <a href="<?php // echo JRoute::_('index.php?option=com_helloworld&task=property.edit&id=' . (int) $item->id) . '&' . JSession::getFormToken() . '=1'; ?>">
                            -->
                            <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit&id=' . (int) $item->id) . '&' . JSession::getFormToken() . '=1'; ?>">
                              <?php echo $this->escape($item->unit_title); ?>
                            </a>
                          <?php else: ?>
                            <?php echo $this->escape($item->unit_title); ?>
                          <?php endif; ?>
                        </td>

            
                        <td>
                          <?php echo JText::_($item->modified); ?>
                        </td>
                      </tr>
                    <?php else : ?>
                      
                    <?php endif; ?>
                  <?php endforeach; ?>
                <input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />

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

