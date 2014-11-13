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

<ul class="nav nav-tabs" id="propertyState">
  <?php
  echo RentalHelper::progressButton($item->id, $item->unit_id, 'propertyversions', 'edit', 'COM_RENTAL_HELLOWORLD_PROPERTY_DETAILS', $item, 'property_id', '', $view);
  echo RentalHelper::progressButton($item->id, $item->unit_id, 'unitversions', 'edit', 'COM_RENTAL_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id', '', $view);
  echo RentalHelper::progressButton($item->id, $item->unit_id, 'images', 'manage', 'IMAGE_GALLERY', $item, 'unit_id', '', $view);
  echo RentalHelper::progressButton($item->id, $item->unit_id, 'tariffs', 'edit', 'COM_RENTAL_SUBMENU_MANAGE_TARIFFS', $item, 'unit_id', '', $view);
  echo RentalHelper::progressButton($item->id, $item->unit_id, 'availability', 'manage', 'COM_RENTAL_SUBMENU_MANAGE_AVAILABILITY', $item, 'unit_id', '', $view);
  echo RentalHelper::progressButton($item->id, $item->unit_id, 'contactdetails', 'edit', 'COM_RENTAL_SUBMENU_MANAGE_CONTACT_DETAILS', $item, 'property_id', '', $view);
  ?>
</ul>
