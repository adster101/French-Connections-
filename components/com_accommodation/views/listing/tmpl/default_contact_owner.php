<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<h4>
  <?php echo htmlspecialchars(JText::_('COM_ACCOMMODATION_CONTACT_THE_OWNER')); ?>
</h4> 
<p>
  <?php if ($this->item->use_invoice_details) : ?>
    <?php echo $this->escape($this->item->firstname); ?>&nbsp;<?php echo $this->escape($this->item->surname); ?><br />
  <?php else: ?>
    <?php echo $this->escape($this->item->alt_first_name); ?>&nbsp;<?php echo $this->escape($this->item->alt_surname); ?><br />
  <?php endif; ?>
  <span class="small">(<?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_ADVERTISING_SINCE', $this->item->advertising_since)); ?>)</span>
</p>
<p>
  <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL'); ?>
  <?php echo ($this->item->use_invoice_details) ? $this->item->phone_1 : $this->item->alt_phone_1; // Assumes there is at least one phone  ?>
</p>

<?php if ($this->item->use_invoice_details) : // Show owners second phone number if there is one on the account   ?>
  <?php if (!empty($this->item->phone_2)) : ?>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL2'); ?>
      <?php echo $this->item->phone_2; ?>
    </p>
  <?php endif; ?>
<?php else: // Show the alt second phone number if one has been entered  ?>
  <?php if (!empty($this->item->alt_phone_2)) : ?>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL2'); ?>
      <?php echo $this->item->alt_phone_2; ?>
    </p>
  <?php endif; ?>
<?php endif; ?>
<?php if ($this->item->use_invoice_details) : // Show owners third phone number if there is one on the account   ?>
  <?php if (!empty($this->item->phone_3)) : ?>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL3'); ?>
      <?php echo $this->item->phone_3; ?>
    </p>
  <?php endif; ?>
<?php else: // Show the alt third phone number if one has been entered  ?>
  <?php if (!empty($this->item->alt_phone_3)) : ?>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL3'); ?>
      <?php echo $this->item->alt_phone_3; ?>
    </p>
  <?php endif; ?>
<?php endif; ?> 
<?php if (count($langs_array) > 0) : ?>
  <p><?php echo JText::sprintf('COM_ACCOMMODATION_LANGUAGES_SPOKEN', implode(', ', $langs_array)); ?></p>
<?php endif; ?>
<?php if ($this->item->booking_form) : ?>
  <?php $link = JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id) . '&tmpl=component&view=bookingform' . $append; ?>
  <p><?php echo JText::sprintf('COM_ACCOMMODATION_BOOKING_FORM_VIEW', $link); ?></p>
<?php endif; ?>

<?php if ($this->item->website && $this->item->website_visible) : ?>
  <p>
    <?php echo JText::_('COM_ACCOMMODATION_CONTACT_WEBSITE'); ?>
    <a target="_blank" rel="nofollow" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id) . '&' . JSession::getFormToken() . '=1&task=listing.viewsite'; ?>">
      <?php echo JText::_('COM_ACCOMMODATION_CONTACT_WEBSITE_VISIT'); ?>
    </a>
  </p>
<?php endif; ?>
<p>
  <strong>
    <?php echo JText::_('COM_ACCOMMODATION_CONTACT_PLEASE_MENTION'); ?>
  </strong>
</p>