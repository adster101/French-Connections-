<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$input = $app->input;

$start_date = $input->get('start_date', '');
$end_date = $input->get('end_date', '');

$doc = JDocument::getInstance();



$availability = $app->getUserState('com_accommodation.enquiry.availability');
$enquiry_data = $app->getUserState('com_accommodation.enquiry.data');

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
      <dd><?php echo '&pound;' . round($availability->CorrectPrice * 0.3); ?></dd>      
    </dl> 
    <p>To pay on arrival</p>
    <?php echo $this->item->additional_price_notes ?>
  </div>
</div>   