<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tariffs = $displayData->tariffs;
$base_currency = $displayData->base_currency;
$exchange_rate_eur = $displayData->exchange_rate_eur;
$exchange_rate_usd = $displayData->exchange_rate_usd;
$tariffs_based_on = $displayData->tariffs_based_on;
?>


<table class="table table-striped">
  <thead>
  <th><?php echo JText::_('COM_ACCOMMODATION_PERIOD'); ?></th>
  <th><?php echo JText::_('COM_ACCOMMODATION_TARIFFS'); ?></th>
</thead>
<?php foreach ($tariffs as $tariff) : ?>

  <?php $prices = JHtml::_('general.price', $tariff->tariff, $base_currency, $exchange_rate_eur, $exchange_rate_usd); ?>
  <tr>
    <td>
      <?php echo htmlspecialchars(JFactory::getDate($tariff->start_date)->calendar('d F Y')); ?>
      &ndash;
      <?php echo htmlspecialchars(JFactory::getDate($tariff->end_date)->calendar('d F Y')); ?>
    </td> 
    <td>
      <?php if ($this->item->base_currency == 'GBP') : ?>
        &pound;<?php echo $prices['GBP'] . '&nbsp;' . htmlspecialchars($tariffs_based_on); ?>  
        <br /> <span class="muted">(<i>Approximately:</i> &euro;<?php echo $prices['EUR']; ?> / &dollar;<?php echo $prices['USD']; ?>)</span>
      <?php else: ?>
        &euro;<?php echo $prices['EUR'] . '&nbsp;' . htmlspecialchars($tariffs_based_on); ?>  
        <br /> <span class="muted">(<i>Approximately:</i> &pound;<?php echo $prices['GBP']; ?> / &dollar;<?php echo $prices['USD']; ?>)</span>  
      <?php endif; ?>
    </td>
  </tr>
<?php endforeach; ?>
</table>