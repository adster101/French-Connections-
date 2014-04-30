<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
JHtml::_('formbehavior.chosen', 'select');

$canChange = $user->authorise('core.edit.state', 'com_autorenewals');

$colspan = (isset($this->items[0])) ? count(get_object_vars($this->items[0])) + 1 : $colspan = 7;
?>

<form action="<?php echo JRoute::_('index.php?option=com_autorenewals'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">

    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <?php
      // Search tools bar
      echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
      ?>
      <table class="table table-striped" id="articleList">
        <thead>
          <tr>
            <th width="1%" class="nowrap center hidden-phone">
              <?php echo JHtml::_('grid.checkall'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_AUTORENEWAL_PROPERTY_ID'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_AUTORENEWAL_PROPERTY_EXPIRY_DATE'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_AUTORENEWAL_CARD_TYPE'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_AUTORENEWAL_LAST_FOUR_DIGITS_OF_CARD'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_AUTORENEWAL_CARD_EXPIRY_DATE'); ?>
            </th>
          </tr>
        </thead>
        <tbody>      
          <?php if (!empty($this->items)) : ?>
            <?php foreach ($this->items as $i => $item): ?>
              <?php $trash = ($item->VendorTxCode) ? true : false; ?>
              <tr>
                <td>
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>

                <td>
                  <?php echo $this->escape($item->id); ?>
                </td>
                <td>
                  <?php echo $this->escape(JFactory::getDate($item->expiry_date)->calendar('d M Y')); ?>
                </td>
                <td>
                  <?php echo $this->escape($item->CardType); ?>
                </td>
                <td>
                  <?php echo $this->escape($item->CardLastFourDigits); ?>
                </td>
                <td>
                  <?php echo $this->escape(JFactory::getDate($item->CardExpiryDate)->calendar('d M Y')); ?>
                </td>
              </tr>					
            <?php endforeach; ?>
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
      <?php echo JHtml::_('form.token'); ?>
    </div>
</form>