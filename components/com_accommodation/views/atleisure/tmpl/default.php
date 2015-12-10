<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

$doc = JDocument::getInstance();

// Include the JDocumentRendererMessage class file
require_once JPATH_ROOT . '/libraries/joomla/document/html/renderer/message.php';
$render = new JDocumentRendererMessage($doc);

$enquiry_data = $app->getUserState('com_accommodation.enquiry.data');
$Itemid_property = SearchHelper::getItemid(array('component', 'com_accommodation'));
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');

$accepted_methods = array('Mastercard', 'VISA', 'American Express', 'Maestro', 'PayPal');
$total_payable = $this->booking_urls->FirstTermAmount + $this->booking_urls->SecondTermAmount;

// TO DO - Should also add a
$owner = JFactory::getUser($this->item->created_by)->username;

$success = 'index.php?option=com_accommodation&Itemid=' . (int) $Itemid_property . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . '&view=enquiry';
?>

<div class="booking-steps">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <p class="active"><span class="active">1</span>&nbsp;Your details</p>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <p class="text-center"><span>2</span>&nbsp;Payment</p>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <p class="text-right"><span class="step">3</span>&nbsp;Confirmation</p>
      </div>
    </div>
  </div>
</div>
<div class="container">

  <div class="row"> 

    <div class="col-lg-8 col-md-8 col-sm-7"> 
      <?php if (count($errors > 0)) : ?>

          <div class="contact-error">
            <?php echo $render->render($errors); ?>
          </div>

      <?php endif; ?>
      <h2 class="">
        <?php echo $this->escape($this->document->title) ?>
      </h2>

      <?php $modules = JModuleHelper::getModules('postenquiry'); //If you want to use a different position for the modules, change the name here in your override.   ?>
      <?php echo $this->loadTemplate($owner . '_form'); ?>

    </div>
    <div class="col-lg-4 col-md-4 col-sm-5">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4>Booking summary</h4>
        </div>
        <div class="panel-body">
                 <h5>
            <?php echo $this->item->unit_title ?>
          </h5>
          <p>
            <img class="img-responsive" src="<?php echo JURI::getInstance()->toString(array('scheme')) . $this->images[0]->url_thumb; ?>" />
          </p>
          <dl class="dl-horizontal">
            <dt><?php echo JText::_('COM_ACCOMMODATION_ENQUIRY_START_DATE_LABEL') ?></dt>
            <dd><?php echo $enquiry_data['start_date'] ?></dd>
            <dt><?php echo JText::_('COM_ACCOMMODATION_ENQUIRY_END_DATE_LABEL') ?></dt>
            <dd><?php echo $enquiry_data['end_date'] ?></dd>
          </dl> 
          <p>To pay on arrival</p>

          <?php echo $this->item->additional_price_notes ?>
          <p>
            Total:
            <span class="pull-right"><?php echo $enquiry_data->CorrectPrice; ?></span>
          </p>
          <p>
            <strong>To pay now:         </strong>
            <span class="pull-right"><?php echo round($enquiry_data->CorrectPrice * 0.3); ?></span>
          </p>
        </div>
      </div>

    </div>
  </div>
</div> 