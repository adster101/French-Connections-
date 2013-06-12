<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('dropdown.init');

$arr = JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

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
      <hr />
      <h4>Expiry date filters</h4>
      <?php echo JHtml::_('calendar', $expiry_start_date, 'expiry_start_date', 'expiry_start_date', '%Y-%m-%d', array()); ?>
      <?php echo JHtml::_('calendar', $expiry_end_date, 'expiry_end_date', 'expiry_end_date', '%Y-%m-%d', array()); ?>

      <div class="btn-group hidden-phone pull-right">
        <button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
        <button class="btn tip hasTooltip" type="button" onclick="document.id('expiry_start_date').value='';document.id('expiry_end_date').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
      </div>

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
      <?php if (empty($this->items)) : // This user doesn't have any listings against their account   ?>
        <hr />
        <div class="alert alert-block">
          <strong><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_NO_LISTINGS'); ?><strong>
              </div>
              <hr/>
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
                      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_REVIEW_STATUS'); ?>
                    </th>
                    <th>
                      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_GREETING'); ?>
                    </th>
                    <th width="10%">
                      <?php if ($canDo->get('helloworld.sort.expiry')) : ?>
                        <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_EXPIRY', 'expiry_date', $listDirn, $listOrder); ?>
                      <?php else: ?>
                        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_EXPIRY'); ?>
                      <?php endif; ?>
                    </th>
                    <th width="15%">
                      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_LISTING_STATUS'); ?>
                    </th>
                    <th width="10%">
                      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_LISTING_STATS'); ?>
                    </th>
                    <th>
                      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_MODIFIED'); ?>
                    </th>

                    <?php if ($canDo->get('helloworld.display.owner')) : ?>
                      <th>
                        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_CREATED_BY'); ?>
                      </th>
                    <?php endif; ?>

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

                    $days_to_renewal = HelloWorldHelper::getDaysToExpiry($item->expiry_date);

                    $auto_renew = (!empty($item->VendorTxCode)) ? true : false;

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
                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'listings.', $canPublish, 'cb', $item->created_on, $item->expiry_date); ?>
                          </td>
                        <?php endif; ?>
                        <td>
                          <?php echo JHtml::_('jgrid.state', JHtmlProperty::reviewStates(), $item->review, $i, 'listings.', $enabled); ?>
                        </td>
                        <td>
                          <?php if ($item->review != 2) : ?>
                            <!--
                              <a href="<?php // echo JRoute::_('index.php?option=com_helloworld&task=property.edit&id=' . (int) $item->id) . '&' . JSession::getFormToken() . '=1';             ?>">
                            -->
                            <?php echo $this->escape($item->title); ?>
                            <br>
                            <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=listing.view&id=' . (int) $item->id) . '&' . JSession::getFormToken() . '=1'; ?>">
                              <?php if ($days_to_renewal <= 7 && !empty($days_to_renewal)) : ?>
                                <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LESS_THAN_7_DAYS_TO_RENEWAL'); ?>
                              <?php else: ?>
                                <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_MORE_THAN_7_DAYS_TO_RENEWAL'); ?>
                              <?php endif; ?>
                              </span>
                            </a>
                          <?php else: ?>
                            <?php echo $this->escape($item->title); ?>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php echo $item->expiry_date; ?>
                          <?php if ($days_to_renewal <= 28 && $days_to_renewal >= 0 && !empty($days_to_renewal)) : // Property is expiring in the next 28 days ?>
                            <p>
                              <?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_DAYS_TO_RENEWAL', $days_to_renewal); ?>
                            </p>
                          <?php elseif ($days_to_renewal < 0) : // Property must have expired ?>
                            <p>
                              <?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_PROPERTY_EXPIRED'); ?>
                            </p>
                          <?php endif; ?>
                        </td>
                        <td>

                          <?php if ($item->review != 2) : ?>
                            <p>
                              <?php echo JHtml::_('property.autorenewalstate', $auto_renew, $item->id); ?>
                            </p>
                            <p>
                              <?php echo JHtml::_('property.renewalButton', $days_to_renewal, $item->id); ?>
                            </p>
                          <?php else: ?>
                            Locked for edit
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php echo JHtml::_('property.stats', $item->id, $item->created_by); ?>
                        </td>
                        <td>
                          <?php echo JText::_($item->modified); ?>
                        </td>
                        <?php if ($canDo->get('helloworld.display.owner')) : ?>
                          <td>
                            <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>">
                              <?php echo JText::_($item->name); ?>
                            </a>
                            <br />
                            <span class="small muted">
                              <a href="mailto:<?php echo JText::_($item->email); ?>"><?php echo JText::_($item->email); ?></a>
                              <br />
                              <?php echo JText::_($item->phone_1); ?>
                            </span>
                            <?php if ($canDo->get('helloworld.view.notes')) : ?>
                              <p>
                                <?php echo JHtml::_('property.notes', $item->id); ?>
                                &nbsp;
                                <?php echo JHtml::_('property.addNote', $item->id); ?>
                              </p>

                            <?php endif; ?>
                          </td>
                        <?php endif; ?>
                      </tr>
                    <?php else : ?>
                    <?php endif; ?>
                  <?php endforeach; ?>
                <input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />

                </tbody>

                <tfoot>
                  <?php
                  if (isset($this->items[0])) {
                    $colspan = count(get_object_vars($this->items[0]));
                  } else {
                    $colspan = 10;
                  }
                  ?>
                  <tr>
                    <td colspan="<?php echo $colspan ?>">
                      <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                  </tr>
                </tfoot>
              </table>
            <?php endif; ?>


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

