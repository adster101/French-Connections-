<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

$layout = $app->input->getCmd('layout', 'default');
?>
<div class="booking-steps">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <p class="<?php echo ($layout == 'default') ? 'active' : '' ?>"><span class="active">1</span>&nbsp;Your details</p>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <p class="<?php echo ($layout == 'payment') ? 'active' : '' ?> text-center"><span>2</span>&nbsp;Payment</p>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <p class="text-right"><span class="step">3</span>&nbsp;Confirmation</p>
      </div>
    </div>
  </div>
</div>