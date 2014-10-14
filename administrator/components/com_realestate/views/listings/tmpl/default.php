<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal', 'a.modal');


$arr = JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$listDirn = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));
$start_date = $this->state->get('filter.start_date');
$end_date = $this->state->get('filter.end_date');
$date_filter = $this->state->get('filter.date_filter');

$canDo = PropertyHelper::getActions();
?>

<form action="<?php echo JRoute::_('index.php?option=com_realestate'); ?>" method="post" name="adminForm" class="form-validate js-stools-form" id="adminForm">
  <?php if ($canDo->get('realestate.listings.filter')) : // Don't show this for owners  ?>
    <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
  <?php endif; ?>
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>

      <?php if (empty($this->items)) : // This user doesn't have any listings against their account      ?>
        <hr />
        <div class="alert alert-block">
          <strong><?php echo JText::_('COM_REALESTATE_LISTING_NO_LISTINGS'); ?><strong>
              </div>
              <hr/>
            <?php else: ?>
              <table class="table table-striped" id="articleList">
                <thead>
                  <tr>
                    <th>
                      <?php echo JHtml::_('searchtools.sort', 'COM_REALESTATE_LISTING_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                    </th>
                    <th width="2%">
                      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <?php if ($canDo->get('core.edit.state')) : ?>
                      <th>
                        <?php echo JText::_('COM_REALESTATE_LISTING_HEADING_ACTIVE'); ?>
                      </th>
                    <?php endif; ?>
                    <th>
                      <?php echo JText::_('COM_REALESTATE_LISTING_HEADING_GREETING'); ?>
                    </th>
                    <th width="10%">
                      <?php echo JHtml::_('searchtools.sort', 'COM_REALESTATE_LISTING_HEADING_DATE_EXPIRY', 'a.expiry_date', $listDirn, $listOrder); ?>
                    </th>
                    <th width="15%">
                      <?php echo JText::_('COM_REALESTATE_LISTING_HEADING_LISTING_STATUS'); ?>
                    </th>
                    <th>
                      <?php echo JText::_('COM_REALESTATE_LISTING_HEADING_DATE_MODIFIED'); ?>
                    </th>
                    <th>
                      <?php echo JHtml::_('searchtools.sort', 'COM_REALESTATE_LISTING_HEADING_DATE_CREATED', 'a.created_on', $listDirn, $listOrder); ?>
                    </th>
                    <?php if ($canDo->get('realestate.listing.review')) : ?>  
                      <th width="10%">
                        <?php echo JText::_('COM_REALESTATE_LISTING_HEADING_REVIEW_STATUS'); ?>
                      </th>
                    <?php endif; ?>
                    <?php if ($canDo->get('realestate.listings.showowner')) : ?>
                      <th>
                        <?php echo JText::_('COM_REALESTATE_LISTING_HEADING_CREATED_BY'); ?>
                      </th>
                    <?php endif; ?>
                    <?php if (property_exists($this->items[0], 'value')) : ?>
                      <th>    
                        <?php echo JHtml::_('searchtools.sort', 'COM_RENTAL_LISTING_HEADING_VALUE', 'a.value', $listDirn, $listOrder); ?>
                      </th>
                    <?php endif; ?>
                  </tr>
                </thead>
                <?php
                $canEditOwn = $canDo->get('core.edit.own');
                $canPublish = $canDo->get('core.edit.state');
                $canReview = $canDo->get('realestate.listing.review', false);
                $canCheckin = true;
                ?>
                <tbody>
                  <?php foreach ($this->items as $i => $item): ?>
                    <?php
                    $days_to_renewal = PropertyHelper::getDaysToExpiry($item->expiry_date);
                    $auto_renew = (!empty($item->VendorTxCode)) ? true : false;

                    if ($item->review == 0)
                    {
                      $enabled = false;
                    }
                    elseif ($item->review == 1)
                    {
                      $enabled = $canDo->get('realestate.listing.submit');
                      $enabled = false;
                    }
                    elseif ($item->review == 2)
                    {
                      $enabled = $canDo->get('realestate.listing.review');
                    }
                    elseif ($item->review == -1)
                    {
                      $enabled = false;
                    }
                    ?>
                    <?php if ($canEditOwn) : ?>
                      <tr>
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
                          <?php echo JHtml::_('image', 'images/property/' . $item->id . '/thumb/' . $item->thumbnail, '') ?>
                          <?php if ($item->review != 2) : ?>
                          <br />
                            <a href="<?php echo JRoute::_('index.php?option=com_realestate&task=listing.edit&id=' . (int) $item->id) ?>">
                              <?php echo JText::_('COM_REALESTATE_LISTING_EDIT_PROPERTY'); ?>
                            </a>                        
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php echo $item->expiry_date; ?>
                          <?php if ($days_to_renewal <= 28 && $days_to_renewal >= 0 && !empty($days_to_renewal)) : // Property is expiring in the next 28 days ?>
                            <p>
                              <?php echo JText::sprintf('COM_REALESTATE_LISTING_DAYS_TO_RENEWAL', $days_to_renewal); ?>
                            </p>
                          <?php elseif ($days_to_renewal < 0) : // Property must have expired  ?>
                            <p>
                              <?php echo JText::sprintf('COM_REALESTATE_LISTING_PROPERTY_EXPIRED'); ?>
                            </p>
                          <?php endif; ?>
                        </td>
                        <td>
                          <p>
                            <?php echo JHtml::_('property.renewalButton', $days_to_renewal, $item->id, $item->review, $canReview, $item->expiry_date); ?>
                          </p>
                        </td>
                        <td>
                          <?php echo JText::_($item->modified); ?>
                        </td>
                        <td>
                          <?php echo JText::_($item->created_on); ?>
                        </td>
                        <?php if ($canDo->get('realestate.listing.review')): ?>
                          <td>
                            <?php if ($item->checked_out) : ?>
                              <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'listing.', $canCheckin); ?>
                            <?php else: ?>
                              <?php // echo JHtml::_('jgrid.state', JHtmlProperty::reviewStates(), $item->review, $i, 'listing.', $enabled); ?>
                            <?php endif; ?>
                          </td>
                        <?php endif ?>                        
                        <?php if ($canDo->get('realestate.listings.showowner')) : ?>
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
                            <?php if ($canDo->get('realestate.notes.view')) : ?>
                              <p>
                                <?php echo JHtml::_('property.notes', $item->id); ?>
                                &nbsp;
                                <?php //echo JHtml::_('property.stats', $item->id, $item->created_by); ?>
                              </p>
                            <?php endif; ?>
                            <?php if (property_exists($item, 'enquiries')) : ?>
                              <?php echo JText::sprintf('COM_RENTAL_PROPERTY_LISTING_ENQUIRY_CLICK_COUNT', $item->enquiries, $item->clicks); ?>
                            <?php endif; ?>
                          </td>
                        <?php endif; ?>
                        <?php if (property_exists($item, 'value')) : ?>
                          <td>
                            <?php echo (round($item->value > 0)) ? '&pound;' . round($item->value, 2) : '' ?> 
                          </td>
                        <?php endif; ?>
                      </tr>
                    <?php else : ?>
                    <?php endif; ?>
                  <?php endforeach; ?>
                <input type="hidden" name="extension" value="<?php echo 'com_realestate'; ?>" />

                </tbody>

                <tfoot>
                  <?php
                  if (isset($this->items[0]))
                  {
                    $colspan = count(get_object_vars($this->items[0]));
                  }
                  else
                  {
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
              <?php echo JHtml::_('form.token'); ?>
            </div>
            </div>      

            </form>  





