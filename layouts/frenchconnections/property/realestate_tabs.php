<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');

// Take the data from the object passed into the template...
$data = $displayData['status'];

?>

<ul class="nav nav-pills" id="propertyState">
  <?php
  echo JHtml::_('property.progressTab', $data->property_detail, 'COM_REALESTATE_PROPERTY_DETAIL','index.php?option=com_realestate&task=propertyversions.edit&realestate_property_id=' . (int) $data->id );
  echo JHtml::_('property.progressTab', $data->gallery, 'IMAGE_GALLERY', 'index.php?option=com_realestate&task=images.edit&realestate_property_id=' . (int) $data->id );
  ?>
</ul>
