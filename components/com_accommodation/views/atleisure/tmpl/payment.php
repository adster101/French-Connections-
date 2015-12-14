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

<?php echo $this->loadTemplate('steps');?> 

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


      <?php echo $this->loadTemplate($owner . '_payment_form'); ?>

    </div>
  </div>
</div> 