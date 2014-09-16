<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');

$listing_id = $input->get('realestate_property_id', '', 'int');


// Take the data from the object passed into the template...
$data = $displayData;
?>

<ul class="nav nav-pills" id="propertyState">
  <?php
  echo FcHtmlProperty::progressTab($item->id, $item->unit_id, 'propertyversions', 'edit', 'compass', 'COM_RENTAL_HELLOWORLD_PROPERTY_DETAILS', $item, 'property_id', '', $view);
  echo FcHtmlProperty::progressTab($item->id, $item->unit_id, 'unitversions', 'edit', 'home', 'COM_RENTAL_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id', '', $view);
  ?>
</ul>
