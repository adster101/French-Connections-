<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$Itemid = SearchHelper::getItemid(array('component','com_accommodation'));

?>
<?php if ($this->item->booking_form) : ?>
  <div class="container">  
    <p class="pull-right">
      <img src="/images/general/logo-3.png" alt="French Connections"> 
    </p>
    <h1><?php echo JText::sprintf('COM_ACCOMMODATION_BOOKING_FORM_TITLE', $this->item->unit_title) ?></h1>
    <p class="lead"><?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_STRAPLINE'); ?></p>
    <div class="print-button-bar">  
      <hr />
      <button class="btn btn-primary btn-large" onclick="window.print()">
        <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_PRINT'); ?>
      </button>&nbsp;&nbsp;
      <a class="btn btn-large" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id) . '#email'; ?>">
        <?php echo JText::_('COM_ACCOMMODATION_BOOKING_BACK_TO_PROPERTY'); ?>
      </a>
      <hr />
    </div>

    <table class="table table-bordered">
      <tbody>
        <tr>
          <td colspan="2">
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_FULL_NAME'); ?>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_ADDRESS'); ?>
          </td>
        </tr>
        <tr>
          <td>
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_HOME_TELEPHONE'); ?>
          </td>
          <td>
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_MOBILE_TELEPHONE'); ?>
          </td>
        </tr>
        <tr>
          <td>
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_FAX'); ?>
          </td>
          <td>
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_EMAIL'); ?>
          </td>
        </tr>
        <tr>
          <td>
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_ARRIVAL_DATE'); ?>
          </td>
          <td>
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_DEPARTURE_DATE'); ?>
          </td>
        </tr>
        <tr>
          <td>
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_ADULTS'); ?>
          </td>
          <td>
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_CHILDREN'); ?>
          </td>
        </tr>        
        <tr>
          <td colspan="2">
            <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_PARTY_DETAILS'); ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php if (empty($this->item->additional_booking_info)) : ?> 
      <p><?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_OVER_18_STATEMENT'); ?></p>
      <p><?php echo JText::sprintf('COM_ACCOMMODATION_BOOKING_FORM_PAYMENT_TERMS', $this->item->deposit, $this->item->security_deposit, $this->item->payment_deadline, $this->item->payment_deadline); ?></p>
    <?php else: ?>
      <?php echo strip_tags($this->item->additional_booking_info, "<td>,<tr>,<table>,<tbody>,<ul>,<li>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<strong>") ?>
    <?php endif; ?>
    <?php if (!empty($this->item->terms_and_conditions)) : ?>
      <?php echo strip_tags($this->item->terms_and_conditions,"<td>,<tr>,<table>,<tbody>,<ul>,<li>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<strong>") ?>
    <?php endif; ?>
    <p><?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_ARRANGE_INSURANCE'); ?></p>
    <p><?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_SIGNATURE'); ?></p>
    <p><?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_DATE'); ?></p>
    <div class="print-button-bar">  
      <hr />
      <button class="btn btn-primary btn-large" onclick="window.print()">
        <?php echo JText::_('COM_ACCOMMODATION_BOOKING_FORM_PRINT'); ?>
      </button>&nbsp;&nbsp;
      <a class="btn btn-large" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id) . '#email'; ?>">
        <?php echo JText::_('COM_ACCOMMODATION_BOOKING_BACK_TO_PROPERTY'); ?>
      </a>
      <hr />
    </div>
  </div>
<?php endif; ?>
