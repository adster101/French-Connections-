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
// Easier to just foreach ?
$units = RentalHelper::getUnitsById($data['progress']);



// Determine the unit id, if a new unit unit_id = 0 - the listing id is then used as parent in the create unit view
($view == 'propertyversions' || $view == 'contactdetails' ) ? $unit_id = key($units) : $unit_id = $input->get('unit_id', '0', 'int');

// Set the item which is used below to output the tabs
$item = (!empty($unit_id)) ? $units[$unit_id] : RentalHelper::getEmptyUnit($listing_id);

// TODO - The multi drop down mark up needs to be moved into a separate function. 
// JHtmlProperty::progressMultiTabs($data['progress'], $view);
// The above function in turn calls progressButton with the same values as below.
?>

<ul class="nav nav-tabs">
  <?php
  echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'propertyversions', 'edit', 'compass', 'COM_RENTAL_HELLOWORLD_PROPERTY_DETAILS', $item, 'property_id', '', $view);
  echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'unitversions', 'edit', 'home', 'COM_RENTAL_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id', '', $view);
  echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'images', 'manage', 'pictures', 'IMAGE_GALLERY', $item, 'unit_id', '', $view);
  echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'availability', 'manage', 'calendar', 'COM_RENTAL_SUBMENU_MANAGE_AVAILABILITY', $item, 'unit_id', '', $view);
  echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'tariffs', 'edit', 'briefcase', 'COM_RENTAL_SUBMENU_MANAGE_TARIFFS', $item, 'unit_id', '', $view);
  echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'contactdetails', 'edit', 'envelope', 'COM_RENTAL_SUBMENU_MANAGE_CONTACT_DETAILS', $item, 'property_id', '', $view);
  ?>
  <?php if (count($data['progress']) > 1) : ?> 
    <?php echo JHtmlProperty::progressMultiTabs($view, 'edit', '', 'COM_RENTAL_HELLOWORLD_ACCOMMODATION_DETAILS', $data['progress'], 'unit_id', '', $view); ?>
  <?php endif; ?>
</ul>
