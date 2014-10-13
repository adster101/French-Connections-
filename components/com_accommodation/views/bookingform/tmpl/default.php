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
    <p class="lead">Print this form, fill it in and post to the address shown below.</p>
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
    <p>
      <?php echo ($this->item->firstname) ? $this->escape($this->item->firstname) : '' ?> 
      <?php echo ($this->item->surname) ? $this->escape($this->item->surname) . '<br />' : '' ?> 
      <?php echo ($this->item->address1) ? $this->escape($this->item->address1) . '<br />' : '' ?> 
      <?php echo ($this->item->address2) ? $this->escape($this->item->address2) . '<br />' : '' ?> 
      <?php echo ($this->item->city) ? $this->escape($this->item->owner_city) . '<br />' : '' ?> 
      <?php echo ($this->item->region) ? $this->escape($this->item->county) . '<br />' : '' ?> 
      <?php echo ($this->item->postal_code) ? $this->escape($this->item->postal_code) . '<br />' : '' ?> 
      <?php echo ($this->item->country) ? $this->escape($this->item->country) . '<br />' : '' ?> 
    </p>
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
      <?php echo strip_tags($this->item->additional_booking_info, "<td>,<tr>,<table>,<tbody>") ?>
    <?php endif; ?>
    <?php if (!empty($this->item->terms_and_conditions)) : ?>
      <?php echo $this->escape(strip_tags($this->item->terms_and_conditions)) ?>
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
