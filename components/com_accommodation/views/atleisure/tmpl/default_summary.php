<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

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
    <div class="payment-summary">
      <p>          
        <?php echo JText::_('COM_ACCOMMODATION_ENQUIRY_START_DATE_LABEL') ?>
        <span> 
          <?php echo JHtml::_('date', $enquiry_data['start_date'], 'd M Y') ?>
        </span>
      </p>
      <p>
        <?php echo JText::_('COM_ACCOMMODATION_ENQUIRY_END_DATE_LABEL') ?>
        <span>
          <?php echo JHtml::_('date', $enquiry_data['end_date'], 'd M Y') ?>
        </span>
      </p> 
      <p>
        <?php echo JText::_('COM_ACCOMMODATION_ENQUIRY_ADULTS_LABEL') ?>
        <span>
          <?php echo $enquiry_data['adults'] ?>
        </span>
      </p> 
      <p>
        Total
        <span class="pull-right">
          <strong>
              <?php echo '&euro;' . $availability->CorrectPrice; ?>
          </strong>
        </span>
      </p>

      <p>
        To pay now
        <span>
          <strong>
            <?php if ($this->days_to_arrival < 42) : ?>
                <?php echo '&euro;' . round($availability->CorrectPrice, 2); ?>
            <?php else : ?>
                <?php echo '&euro;' . round($availability->CorrectPrice * 0.3, 2); ?>
            <?php endif; ?>
          </strong>
        </span>
      </p>
    </div>
    <h4>Additional costs</h4>
    <?php echo $this->item->additional_price_notes ?>
  </div>
</div>   