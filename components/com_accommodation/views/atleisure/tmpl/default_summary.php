<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

$availability = $app->getUserState('com_accommodation.enquiry.availability');
$enquiry_data = $app->getUserState('com_accommodation.enquiry.data');

$start_dateObj = new DateTime(JHtml::_('date', $enquiry_data['start_date'], 'Y-m-d'));

$now = new DateTime();

$interval = $start_dateObj->diff($now);
$days_to_arrival = $interval->format('%a');
$balance_due_date = $start_dateObj->add('+42 days');

?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h4>Booking summary</h4>
  </div>
  <div class="panel-body">
    <h4>
      <?php echo $this->item->unit_title ?>
    </h4>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_BOOKING_YOUR_DETAILS_LINE'); ?>
    </p>
    <p>
      <img class="img-responsive" src="<?php echo JURI::getInstance()->toString(array('scheme')) . $this->images[0]->url_thumb; ?>" />
    </p>
    <dl class="dl-horizontal">
      <dt><?php echo JText::_('COM_ACCOMMODATION_ENQUIRY_START_DATE_LABEL') ?></dt>
      <dd><?php echo $enquiry_data['start_date'] ?></dd>
      <dt><?php echo JText::_('COM_ACCOMMODATION_ENQUIRY_END_DATE_LABEL') ?></dt>
      <dd><?php echo $enquiry_data['end_date'] ?></dd>
      <dt>Total:</dt>
      <dd><?php echo '&pound;' . $availability->CorrectPrice; ?></dd>
      <dt>To pay now:</dt>
      <?php if ($days_to_arrival < 42) : ?>
          <dd><?php echo '&pound;' . round($availability->CorrectPrice); ?></dd>
      <?php else : ?>
          <dd><?php echo '&pound;' . round($availability->CorrectPrice * 0.3); ?></dd> 
          <dt>Payable on or before - <?php echo $balance_due_date ?></dt>
      <?php endif; ?>
    </dl> 
    <p>To pay on arrival</p>
    <?php echo $this->item->additional_price_notes ?>
  </div>
</div>   