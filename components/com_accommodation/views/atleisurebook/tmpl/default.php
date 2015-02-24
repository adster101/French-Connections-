<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$enquiry_data = $app->getUserState('com_accommodation.enquiry.data');
$Itemid_property = SearchHelper::getItemid(array('component', 'com_accommodation'));
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');
//print_r($this->booking_urls);

$accepted_methods = array('Mastercard', 'VISA', 'American Express', 'Maestro', 'PayPal');
$payment_url = $this->booking_urls->PaymentMethods[17]->URL;
?>
<div class="container">
  <h2 class="page-header">
    <?php echo $this->escape($this->document->title) ?>
  </h2>

  <?php $modules = JModuleHelper::getModules('postenquiry'); //If you want to use a different position for the modules, change the name here in your override.  ?>
  <div class="row">
    <div class="col-lg-8 col-md-8">
      <div class="panel panel-default">
        <div class="panel-body">
          <dl class="dl-horizontal">
            <dt><?php echo JText::_('COM_ACCOMMODATION_AT_LEISURE_BOOKING_REFERENCE') ?>
            <dd><?php echo $this->booking_urls->BookingNumber ?></dd>
            <dt><?php echo JText::_('COM_ACCOMMODATION_AT_LEISURE_TOTAL_PAYABLE') ?>
            <dd><?php echo $this->booking_urls->FirstTermAmount + $this->booking_urls->SecondTermAmount ?></dd>
          </dl>
          <FORM action="<?php echo $payment_url ?>" method="post">
            <INPUT type="hidden"name="urlsuccess" value="https://www.frenchconnections.co.uk"/>
            <INPUT type="hidden"name="urlfailure"value="https://www.frenchconnections.co.uk"/>
            <INPUT type="submit" value="Send">
          </FORM>     
        </div>

      </div>
    </div>

  </div>
</div>