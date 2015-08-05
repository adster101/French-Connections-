<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.modal', 'a.modal');


$arr = JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$listDirn = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));
$start_date = $this->state->get('filter.start_date');
$end_date = $this->state->get('filter.end_date');
$date_filter = $this->state->get('filter.date_filter');
$user = JFactory::getUser();

//$user = JFactory::getUser();
//$userId = $user->get('id');
//$groups = $user->getAuthorisedGroups();
//$ordering = ($listOrder == 'a.lft');
//$originalOrders = array();
//$listing_id = '';

$canDo = RentalHelper::getActions();
?>

<form action="<?php echo JRoute::_('index.php?option=com_rental'); ?>" method="post" name="adminForm" class="form-validate js-stools-form" id="adminForm">
  <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

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
          <strong><?php echo JText::_('COM_RENTAL_HELLOWORLD_NO_LISTINGS'); ?>
          </strong>
        </div>
        <hr/>
      <?php else: ?>
        <table class="table table-striped" id="articleList">
          <thead>
            <tr>
              <th>
                <?php echo JHtml::_('searchtools.sort', 'COM_RENTAL_HELLOWORLD_HEADING_ID', 'id', $listDirn, $listOrder); ?>
              </th>
              <th width="2%">
                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
              </th>
              <?php if ($canDo->get('core.edit.state')) : ?>
                <th>
                  <?php echo JText::_('COM_RENTAL_HELLOWORLD_HEADING_ACTIVE'); ?>
                </th>
              <?php endif; ?>
              <th>
                <?php echo JText::_('COM_RENTAL_HELLOWORLD_HEADING_GREETING'); ?>
              </th>
              <th width="10%">
                <?php echo JHtml::_('searchtools.sort', 'COM_RENTAL_HELLOWORLD_HEADING_DATE_EXPIRY', 'a.expiry_date', $listDirn, $listOrder); ?>
              </th>
              <th width="15%">
                <?php echo JText::_('COM_RENTAL_HELLOWORLD_HEADING_LISTING_STATUS'); ?>
              </th>
              <th>
                <?php echo JHtml::_('searchtools.sort', 'COM_RENTAL_HELLOWORLD_HEADING_DATE_MODIFIED', 'a.modified', $listDirn, $listOrder); ?>
              </th>
              <th>
                <?php echo JHtml::_('searchtools.sort', 'COM_RENTAL_HELLOWORLD_HEADING_DATE_CREATED', 'a.created_on', $listDirn, $listOrder); ?>
              </th>
              <?php if ($canDo->get('rental.listing.review')) : ?>  
                <th width="10%">
                  <?php echo JText::_('COM_RENTAL_HELLOWORLD_HEADING_REVIEW_STATUS'); ?>
                </th>
              <?php endif; ?>
              <?php if ($canDo->get('rental.listings.showowner')) : ?>
                <th>
                  <?php echo JText::_('COM_RENTAL_HELLOWORLD_HEADING_CREATED_BY'); ?>
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
          $canReview = $canDo->get('rental.listing.review', false);
          $canCheckin = true;
          ?>
          <tbody>
            <?php foreach ($this->items as $i => $item): ?>
              <?php
              $days_to_renewal = PropertyHelper::getDaysToExpiry($item->expiry_date);
              $auto_renew = (!empty($item->VendorTxCode)) ? true : false;

              $value = (!empty($item->value)) ? round($item->value, 2) : '';

              if ($item->review == 0)
              {
                $enabled = false;
              }
              elseif ($item->review == 1)
              {
                $enabled = $canDo->get('rental.listing.submit');
              }
              elseif ($item->review == 2)
              {
                $enabled = $canDo->get('rental.listing.review');
              }
              elseif ($item->review == -1)
              {
                $enabled = false;
              }
              ?>
              <?php if ($canEditOwn) : ?>
                <tr <?php echo ($value) ? 'class = \'alert alert-success\'' : '' ?>>
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
                    <?php if (!empty($item->url_thumb)) : ?>

                      <?php $uri = JURI::getInstance(); ?>
                      <img src="<?php echo $uri->getScheme() . '://' . $item->url_thumb ?>" />
                    <?php else: ?>
                      <?php echo JHtml::_('general.image', '/images/property/' . $item->unit_id . '/thumb/' . $item->thumbnail) ?>
                    <?php endif; ?>
                    <?php if ($item->review != 2) : ?>
                      <a href="<?php echo JRoute::_('index.php?option=com_rental&task=listing.view&id=' . (int) $item->id) . '&' . JSession::getFormToken() . '=1'; ?>">
                        <?php if ($days_to_renewal <= 7 && !empty($days_to_renewal)) : ?>
                          <?php echo JText::_('COM_RENTAL_HELLOWORLD_LESS_THAN_7_DAYS_TO_RENEWAL'); ?>
                        <?php else: ?>
                          <?php echo JText::_('COM_RENTAL_HELLOWORLD_MORE_THAN_7_DAYS_TO_RENEWAL'); ?>
                        <?php endif; ?>
                      </a>                        
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php echo $item->expiry_date; ?>
                    <?php if ($days_to_renewal <= 28 && $days_to_renewal >= 0 && !empty($days_to_renewal)) : // Property is expiring in the next 28 days  ?>
                      <p>
                        <?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_DAYS_TO_RENEWAL', $days_to_renewal); ?>
                      </p>
                    <?php elseif ($days_to_renewal < 0) : // Property must have expired   ?>
                      <p>
                        <?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_PROPERTY_EXPIRED'); ?>
                      </p>
                    <?php endif; ?>
                  </td>
                  <td>
                    <p>
                      <?php echo JHtml::_('property.autorenewalstate', $auto_renew, $item->id); ?>
                    </p>
                    <p>
                      <?php echo JHtml::link('index.php?option=com_rental&task=marketing.edit&property_id=' . (int) $item->id, JText::_('COM_RENTAL_SUBMENU_ADDITIONAL_MARKETING')); ?>
                    </p>
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
                  <?php if ($canDo->get('rental.listing.review')): ?>
                    <td>
                      <?php if ($item->review == 2) : ?>
                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'listing.', $canCheckin); ?>
                      <?php else: ?>
                        <?php echo JHtml::_('jgrid.state', JHtmlProperty::reviewStates(), $item->review, $i, 'listing.', $enabled); ?>
                      <?php endif; ?>
                    </td>
                  <?php endif ?>                        
                  <?php if ($canDo->get('rental.listings.showowner')) : ?>
                    <td>
                      <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>">
                        <?php echo JText::_($item->name) ?>
                      </a>
                        <?php echo JText::sprintf('COM_RENTAL_PROPERTY_LISTING_OWNER_ACC_ID',$item->created_by) ?>
                      <br />
                      <span class="small muted">
                        <a href="mailto:<?php echo JText::_($item->email); ?>"><?php echo JText::_($item->email); ?></a>
                        <br />
                        <?php echo JText::_($item->phone_1); ?>
                      </span>
                      <?php if ($user->authorise('core.manage', 'com_notes')) : ?>
                        <p>
                          <?php echo JHtml::_('property.notes', $item->id); ?>
                        </p>
                          <?php
                        $preview = JUri::getInstance('/listing/' . $item->id . '?unit_id=' . $item->unit_id . '&preview=1');
                        $preview->setScheme('http');
                        $preview->setHost('www.frenchconnections.co.uk');
                        echo '<p><a target="_blank" href=' . $preview->toString() . '>Preview</a></p>';
                        ?>
                      <?php endif; ?>
                      <?php if (property_exists($item, 'enquiries')) : ?>
                        <?php echo JText::sprintf('COM_RENTAL_PROPERTY_LISTING_ENQUIRY_CLICK_COUNT', $item->enquiries, $item->clicks); ?>
                      <?php endif; ?>
                      <?php if (property_exists($item, 'existing') ) : ?>
                        <?php echo ($item->existing > 1) ? JText::sprintf('COM_RENTAL_PROPERTY_LISTING_EXISTING_PROPERTY_COUNT', $item->existing) : ''; ?>
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
          <input type="hidden" name="extension" value="<?php echo 'com_rental'; ?>" />

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
    <?php
    $layout = new JLayoutFile('modal', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
    echo $layout->render($data = array('title' => 'Snooze a property', 'id' => 'snooze'));
    echo $layout->render($data = array('title' => 'Change owner', 'id' => 'changeowner'));
    echo $layout->render($data = array('title' => 'Change expiry date', 'id' => 'expirydate'));
    ?>
</form>  





