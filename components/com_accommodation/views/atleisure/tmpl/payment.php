<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

$doc = JDocument::getInstance();

// Include the JDocumentRendererMessage class file
require_once JPATH_ROOT . '/libraries/joomla/document/html/renderer/message.php';
$render = new JDocumentRendererMessage($doc);

$enquiry_data = $app->getUserState('com_accommodation.enquiry.data');
$availability = $app->getUserState('com_accommodation.enquiry.availability');
$booking_info = $app->getUserState('com_accommodation.enquiry.booking_info');

$booking_json = array();

foreach ($booking_info->PaymentMethods as $key => $value)
{
    if (strpos($value->Method, 'PayPal') !== false)
    {
        $paypal = array();
        $paypal['method'] = $value->Method;
        $paypal['mc'] = 'P';
        $paypal['amount'] = $value->Amount;
        $paypal['costs'] = $value->Costs;
        $paypal['costsstring'] = JText::sprintf('COM_ACCOMMODATION_AT_LEISURE_PAYMENT_FEE', $value->Costs, $value->Method);
        $paypal['amountpluscosts'] = $value->Amount + $value->Costs;
        $paypal['orgtot'] = $value->Amount + $booking_info->SecondTermAmount;
        $paypal['orgtotpluscosts'] = $value->Amount + $booking_info->SecondTermAmount + $value->Costs;
        $paypal['URL'] = $value->URL;
        $booking_json['payment1']['PayPal'][$value->Method] = $paypal;
    }
    elseif (strpos($value->Method, 'iDEAL') === false)
    {
        $paypal = array();
        $paypal['method'] = $value->Method;
        $paypal['mc'] = substr($value->Method, 0, 1);
        $paypal['amount'] = $value->Amount;
        $paypal['costs'] = $value->Costs;
        $paypal['costsstring'] = JText::sprintf('COM_ACCOMMODATION_AT_LEISURE_PAYMENT_FEE', $value->Costs, $value->Method);
        $paypal['amountpluscosts'] = $value->Amount + $value->Costs;
        $paypal['orgtot'] = $value->Amount + $booking_info->SecondTermAmount;
        $paypal['orgtotpluscosts'] = $value->Amount + $booking_info->SecondTermAmount + $value->Costs;
        $paypal['URL'] = $value->URL;
        $booking_json['payment1']['creditcard'][$value->Method] = $paypal;
    }
}

$Itemid_property = SearchHelper::getItemid(array('component', 'com_accommodation'));
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');

$accepted_methods = array('Mastercard', 'VISA', 'American Express', 'Maestro', 'PayPal');
$total_payable = $this->booking_urls->FirstTermAmount + $this->booking_urls->SecondTermAmount;

// TO DO - Should also add a
$owner = JFactory::getUser($this->item->created_by)->username;

$success = 'index.php?option=com_accommodation&Itemid=' . (int) $Itemid_property . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . '&view=enquiry';
?>
<script>
    var huovnr = "175497570";
    var huovid = "0x0000000002508380";
    var jsonstring = <?php echo json_encode($booking_json) ?>;
    var woordcreditcard = "Credit card";
</script>

<?php echo $this->loadTemplate('steps'); ?> 

<div class="container">
  <div class="row"> 
    <div class="col-lg-4 col-md-4 col-sm-5 col-lg-push-8 col-md-push-8 col-sm-push-7"> 
      <?php echo $this->loadTemplate('summary'); ?>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-7 col-lg-pull-4 col-md-pull-4 col-sm-pull-5">
      <?php if (count($errors > 0)) : ?>
          <div class="contact-error">
            <?php echo $render->render($errors); ?>
          </div>
      <?php endif; ?>
      <p>Pay online safely and easily! As soon as we receive your payment in full, you will be emailed the travel documents with the address and information about picking up the key.</p>
      <div id="select-amount" class="payment-btn-group clearfix">
        <div id="fullamount" class="col-xs-6 selected" data-amount="<?php echo $booking_info->SecondTermAmount ?>">
          <span class="amount"><span class="currency">€</span> <?php echo $booking_info->SecondTermAmount ?></span>
          <span class="label">total amount</span>
        </div>
        <div id="downpayment" class="col-xs-6" data-amount="<?php echo $booking_info->FirstTermAmount ?>">
          <span class="amount"><span class="currency">€</span> <?php echo $booking_info->FirstTermAmount ?></span>
          <span class="label">deposit</span>
        </div>
      </div>
      <div id="restWarning" class="alert alert-warning" style="display:none;">
        <strong>Please note:</strong> the remaining sum must be paid by <?php echo JHtml::_('date', $booking_info->SecondTermDateTime, 'd M Y') ?>
      </div>
      <hr />
      <h3>Payment method</h3>
      <div id="select-method" class="payment-btn-group">
        <div id="paypal" class="col-sm-6 payment-method">
          <span class="payment-sprite icn-paypal">

          </span>
          <span class="label">PayPal</span>
        </div>
        <div id="creditcard" class="col-sm-6 payment-method">
          <span class="payment-sprite icn-creditcard"></span>
          <span class="label">Creditcard</span>
        </div>        
      </div>
      <div class="row">
        <div id="nomainpaymentmethodwarning" class="col-md-12" style="display:none;margin-top:10px;">
          <p style="text-align:center;color:#D44343;">Selecteer een betaalmethode alstublieft</p>
        </div>
      </div>
      <div id="costsWarning" class="alert alert-warning" style="margin-bottom: 0px; margin-top: 25px; display: none;">
        <strong>Let op:</strong>&nbsp;<span class="costMessage">aan PayPal gebruik zijn kosten (€ 8,18) verbonden.</span>        
      </div>
      <div id="selectCreditCard" class="row subpayment" style="margin-top: 25px; display: none;">
        <div class="col-md-12">
          <div id="costsWarning-creditcard" class="alert alert-warning" style="margin-bottom: 25px;">
            <strong>Let op:</strong>&nbsp;<span class="costMessageCC">aan creditcard gebruik zijn kosten (€ 6,14) verbonden.</span>      
          </div>
        </div>
        <div class="col-sm-4">
          <p style="margin:0px;margin-top: 5px;"><strong>Creditcard</strong></p>
        </div>
        <div class="col-sm-8">
          <select id="creditcardselector" class="selectpicker bs-select-hidden" title="Maak uw keuze...">
            <option value="" class="bs-title-option">Maak uw keuze...</option>
            <option value="A">American Express</option><option value="M">Maestro</option>
            <option value="E">Mastercard</option>
            <option value="V">VISA</option>    
          </select>
        </div>
        
      </div>
      <div id="pay-now" class="alignc">
          <a href="JavaScript:;" id="payment-button" class="butn butn-large">Pay now</a>
        </div>
    </div>
  </div> 
</div>

<?php
var_dump($booking_info);
?>