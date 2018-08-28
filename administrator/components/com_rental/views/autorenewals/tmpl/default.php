<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');

$language = JFactory::getLanguage();

$canChange = 1;

?>
    <form action="<?php echo JRoute::_('index.php?option=com_rental&view=autorenewals&id=' . (int) $this->id) ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
      <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
          <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">

        <?php else : ?>
          <div id="j-main-container">
          <?php endif; ?>
          <?php echo JText::_('COM_RENTAL_HELLOWORLD_AUTORENEWAL_BLURB'); ?>
          <table class="table table-striped" id="articleList">
            <thead>
              <tr>
                <th width="1%" class="nowrap center hidden-phone">
                  <?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th class="nowrap center">
                  <?php echo JText::_('COM_RENTAL_AUTORENEWAL_DEFAULT'); ?>
                </th>
                <th class="center">
                  <?php echo JText::_('COM_RENTAL_AUTORENEWAL_CARD_TYPE'); ?>
                </th>
                <th class="center">
                  <?php echo JText::_('COM_RENTAL_AUTORENEWAL_LAST_FOUR_DIGITS_OF_CARD'); ?>
                </th>
                <th class="center">
                  <?php echo JText::_('COM_RENTAL_AUTORENEWAL_CARD_EXPIRY_DATE'); ?>
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
                    <td class="center">
                      <?php echo JHtml::_('jgrid.isdefault', ($item->current) ? 1 :0, $i, 'autorenewals.',1 ); ?>
                    </td>
                    <td class="center">
                      <?php echo $this->escape($item->CardType); ?>
                    </td>
                    <td class="center">
                      <?php echo $this->escape($item->CardLastFourDigits); ?>
                    </td>
                    <td class="center">
                      <?php echo $this->escape(JFactory::getDate($item->CardExpiryDate)->calendar('d M Y')); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="<?php echo $colspan ?>">
                  <?php //echo $this->pagination->getListFooter(); ?>
                </td>
              </tr>
            </tfoot>
          </table>
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="boxchecked" value="0" />
          <?php echo JHtml::_('form.token'); ?>
        </div>

    </form>
