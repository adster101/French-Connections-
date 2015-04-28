<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');

$listing_id = $input->get('property_id', '', 'int');


// Take the data from the object passed into the template...
$data = $displayData;

$status = $displayData['status'];

$class = (empty($status->expiry_date) && $status->review < 2) ? 'nav nav-wizard clearfix' : 'nav nav-tabs';
$tabs_heading = (empty($status->expiry_date) && $status->review < 2) ? 'COM_PROPERTY_PROPERTY_PROGRESS' : 'COM_RENTAL_PROPERTY_STATUS';
$property_message = ($status->location_detail) ? 'COM_RENTAL_PROGRESS_PROPERTY_DETAIL_COMPLETE' : 'COM_RENTAL_PROGRESS_COMPLETE_PROPERTY_DETAIL';
$unit_message = ($status->units[$status->unit_id]->unit_detail) ? 'COM_RENTAL_PROGRESS_UNIT_DETAIL_COMPLETE' : 'COM_RENTAL_PROGRESS_UNIT_DETAIL_PLEASE_COMPLETE';
$image_message = ($status->units[$status->unit_id]->gallery) ? 'COM_RENTAL_PROGRESS_IMAGES_DETAIL_COMPLETE' : 'COM_RENTAL_PROGRESS_IMAGES_DETAIL_PLEASE_COMPLETE';
$tariff_message = ($status->units[$status->unit_id]->tariffs) ? 'COM_RENTAL_PROGRESS_TARIFF_DETAIL_COMPLETE' : 'COM_RENTAL_PROGRESS_TARIFF_DETAIL_PLEASE_COMPLETE';
$availability_message = ($status->units[$status->unit_id]->availability) ? 'COM_RENTAL_PROGRESS_AVAILABILITY_DETAIL_COMPLETE' : 'COM_RENTAL_PROGRESS_AVAILABILITY_DETAIL_PLEASE_COMPLETE';
$contact_message = ($status->units[$status->unit_id]->availability) ? 'COM_RENTAL_PROGRESS_CONTACT_DETAIL_COMPLETE' : 'COM_RENTAL_PROGRESS_AVAILABILITY_DETAIL_PLEASE_COMPLETE';

// Process the units into a keyed array (useful so we can get at individual units)
$units = RentalHelper::getUnitsById($data['progress']);

// Determine the unit id, if a new unit unit_id = 0 - the listing id is then used as parent in the create unit view
$unit_id = ($view == 'propertyversions' || $view == 'contactdetails' ) ? key($units) : $input->get('unit_id', '0', 'int');

// Set the item which is used below to output the tabs
$item = (!empty($unit_id)) ? $units[$unit_id] : RentalHelper::getEmptyUnit($listing_id);

// TODO - The multi drop down mark up needs to be moved into a separate function. 
// JHtmlProperty::progressMultiTabs($data['progress'], $view);
// The above function in turn calls progressButton with the same values as below.
?>
<h4><?php echo JText::_($tabs_heading); ?></h4>
<?php if (!empty($data->notice)) : ?>
  <div class="alert alert-info">
    <?php echo JText::_($data->notice); ?>
  </div>
<?php endif; ?>
<ul class="<?php echo $class ?>" id="propertyState">
  <?php
  echo JHtml::_('property.progressTab', $status->location_detail, true, $status->expiry_date, 'COM_RENTAL_HELLOWORLD_PROPERTY_DETAILS', 'index.php?option=com_rental&task=propertyversions.edit&property_id=' . (int) $status->id, $property_message, $view, 'propertyversions');
  echo JHtml::_('property.progressTab', $status->units[$unit_id]->unit_detail, $status->location_detail,$status->expiry_date, 'COM_RENTAL_HELLOWORLD_ACCOMMODATION_DETAILS', 'index.php?option=com_rental&task=unitversions.edit&unit_id=' . (int) $status->unit_id, $unit_message, $view, 'unitversions');
  echo JHtml::_('property.progressTab', $status->units[$unit_id]->gallery, $status->units[$unit_id]->unit_detail,$status->expiry_date, 'IMAGE_GALLERY', 'index.php?option=com_rental&task=images.manage&unit_id=' . (int) $status->unit_id, $image_message, $view, 'images');
  echo JHtml::_('property.progressTab', $status->units[$unit_id]->tariffs, $status->units[$unit_id]->gallery,$status->expiry_date, 'COM_RENTAL_SUBMENU_MANAGE_TARIFFS', 'index.php?option=com_rental&task=tariffs.edit&unit_id=' . (int) $status->unit_id, $tariff_message, $view, 'tariffs');
  echo JHtml::_('property.progressTab', $status->units[$unit_id]->availability, $status->units[$unit_id]->tariffs,$status->expiry_date, 'COM_RENTAL_SUBMENU_MANAGE_AVAILABILITY', 'index.php?option=com_rental&task=availability.manage&unit_id=' . (int) $status->unit_id, $availability_message, $view, 'availability');
  echo JHtml::_('property.progressTab', $status->contact_detail, $status->units[$unit_id]->availability,$status->expiry_date, 'COM_RENTAL_SUBMENU_MANAGE_CONTACT_DETAILS', 'index.php?option=com_rental&task=contactdetails.edit&property_id=' . (int) $status->id, $contact_message, $view, 'contactdetails');

  if (empty($status->expiry_date) && $status->review < 2)
  {
    echo JHtml::_('property.progressTab', $status->payment, $status->complete,$status->expiry_date, 'PAYMENT', 'index.php?option=com_rental&task=payment.summary&id=' . (int) $status->id, '', $view, 'payment');
  }
  ?>
</ul>
