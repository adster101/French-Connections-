<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<table class="table table-striped">
  <thead>
  <th><?php echo JText::_('COM_ACCOMMODATION_PERIOD'); ?></th>
  <th><?php echo JText::_('COM_ACCOMMODATION_TARIFFS'); ?></th>
</thead>
<?php foreach ($this->tariffs as $tariff) : ?>
  <?php $prices = JHtml::_('general.price', $tariff->tariff, 'GBP', $this->item->base_currency, $this->item->exchange_rate_usd); ?>
  <tr>
    <td>
      <?php echo htmlspecialchars(JFactory::getDate($tariff->start_date)->calendar('d F Y')); ?>
      &ndash;
      <?php echo htmlspecialchars(JFactory::getDate($tariff->end_date)->calendar('d F Y')); ?>
    </td> 
    <td>
      <?php if ($this->item->base_currency == 'GBP') : ?>
        &pound;<?php echo $prices['GBP'] . '&nbsp;' . htmlspecialchars($this->item->tariffs_based_on); ?>  
        <br /> <span class="muted">(<i>Approximately:</i> &euro;<?php echo $prices['EUR']; ?> / &dollar;<?php echo $prices['USD']; ?>)</span>
      <?php else: ?>
        &euro;<?php echo $prices['EUR'] . '&nbsp;' . htmlspecialchars($this->item->tariffs_based_on); ?>  
        <br /> <span class="muted">(<i>Approximately:</i> &pound;<?php echo $prices['GBP']; ?> / &dollar;<?php echo $prices['USD']; ?>)</span>  
      <?php endif; ?>
    </td>
  </tr>
<?php endforeach; ?>
</table>