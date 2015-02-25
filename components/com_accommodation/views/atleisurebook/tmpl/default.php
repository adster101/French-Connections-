<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$enquiry_data = $app->getUserState('com_accommodation.enquiry.data');
$Itemid_property = SearchHelper::getItemid(array('component', 'com_accommodation'));
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');

$accepted_methods = array('Mastercard', 'VISA', 'American Express', 'Maestro', 'PayPal');
$total_payable = $this->booking_urls->FirstTermAmount + $this->booking_urls->SecondTermAmount;
?>
<div class="container">
  <h2 class="page-header">
    <?php echo $this->escape($this->document->title) ?>
  </h2>

  <?php $modules = JModuleHelper::getModules('postenquiry'); //If you want to use a different position for the modules, change the name here in your override.  ?>

  <div class="row"> 
    <div class="col-lg-8 col-md-8 col-sm-7"> 
      <?php echo JText::_('COM_ACCOMMODATION_AT_LEISURE_BOOKING_SUMMARY') ?>
      <hr />
      <dl class="dl-horizontal">
        <dt><?php echo JText::_('COM_ACCOMMODATION_AT_LEISURE_BOOKING_REFERENCE') ?></dt>
        <dd><?php echo $this->booking_urls->BookingNumber ?></dd>
        <dt><?php echo JText::_('COM_ACCOMMODATION_AT_LEISURE_BOOKING_DEPOSIT') ?></dt>
        <dd><?php echo JText::sprintf('COM_ACCOMMODATION_AT_LEISURE_BOOKING_DEPOSIT_AMOUNT', $this->booking_urls->FirstTermAmount) ?></dd>
        <dt><?php echo JText::_('COM_ACCOMMODATION_AT_LEISURE_BOOKING_BALANCE') ?></dt>
        <dd><?php echo JText::sprintf('COM_ACCOMMODATION_AT_LEISURE_BOOKING_BALANCE_AMOUNT', $this->booking_urls->SecondTermAmount, JHtml::_('date', $this->booking_urls->SecondTermDateTime, 'd M Y')) ?></dd>
        <dt><?php echo JText::_('COM_ACCOMMODATION_AT_LEISURE_TOTAL_PAYABLE') ?></dt>
        <dd><?php echo '&euro;' . $total_payable ?></dd>
      </dl>
      <FORM action="#" method="post" class="atleisure-booking-form">
        <fieldset>
          <legend>
            <?php echo JText::_('COM_ACCOMMODATION_AT_LEISURE_BOOKING_PAYMENT_OPTIONS'); ?>
          </legend>
          <?php foreach ($this->booking_urls->PaymentMethods as $key => $option) : ?>
            <?php if (in_array($option->Method, $accepted_methods)): ?>  
              <label> 
                <input name="option" type="radio" value="<?php echo $option->URL ?>" />
                <?php echo $option->Method ?>
                <?php echo '&euro;' . $option->Amount; ?>  
                <?php echo (!empty($option->Costs)) ? '(&euro;' . $option->Costs . ')' : ''; ?> 
              </label>
              <br />
            <?php endif; ?>
          <?php endforeach; ?>   
          <hr />
          <button class="btn btn-primary btn-lg"><?php echo JText::_('COM_ACCOMMODATION_AT_LEISURE_PAY_NOW') ?></button>
          <INPUT type="hidden" name="urlsuccess" value="http://www.frenchconnections.co.uk"/>
          <INPUT type="hidden" name="urlfailure"value="http://www.frenchconnections.co.uk"/>
        </fieldset>  
      </form>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-5">
      <div class="panel panel-default">
        <div class="panel-body">
          <img class="img-responsive" src="<?php echo JURI::getInstance()->toString(array('scheme')) . $this->images[0]->url_thumb; ?>" />
          <?php echo $this->item->unit_title ?>
        </div>
      </div>
    </div>

  </div>
</div> 