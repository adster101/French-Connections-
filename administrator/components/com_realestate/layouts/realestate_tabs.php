<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');

// Take the data from the object passed into the template...
$data = $displayData['status'];
$class = (empty($data->expiry_date) && $data->review < 2) ? 'nav nav-wizard clearfix' : 'nav nav-tabs';

$property_message = ($data->property_detail) ? 'COM_REALESTATE_PROGRESS_PROPERTY_DETAIL_COMPLETE' : 'COM_REALESTATE_PROGRESS_COMPLETE_PROPERTY_DETAIL';
$image_message = ($data->gallery) ? 'COM_REALESTATE_PROGRESS_IMAGE_DETAIL_COMPLETE' : 'COM_REALESTATE_PROGRESS_COMPLETE_IMAGE_DETAIL';
$tabs_heading = (empty($data->expiry_date) && $data->review < 2) ? 'COM_PROPERTY_PROPERTY_PROGRESS' : 'COM_REALESTATE_PROPERTY_STATUS';
  
?>
<h4><?php echo JText::_($tabs_heading); ?></h4>
<?php if (!empty($data->notice)) : ?>
<div class="alert alert-info">
  <?php echo JText::_($data->notice); ?>
</div>
<?php endif; ?>


<ul class="<?php echo $class ?>">
  <?php
  echo JHtml::_('property.progressTab', $data->property_detail, true, 'COM_REALESTATE_PROPERTY_DETAIL', 'index.php?option=com_realestate&task=propertyversions.edit&realestate_property_id=' . (int) $data->id, $property_message, $view, 'propertyversions');
  echo JHtml::_('property.progressTab', $data->gallery, $data->property_detail, 'IMAGE_GALLERY', 'index.php?option=com_realestate&task=images.manage&realestate_property_id=' . (int) $data->id, $image_message, $view, 'images');
  if (empty($data->expiry_date) && $data->review < 2)
  {
    echo JHtml::_('property.progressTab', $data->payment, $data->complete, 'PAYMENT', 'index.php?option=com_realestate&task=payment.summary&realestate_property_id=' . (int) $data->id, 'Wooty woot', $view, 'payment');
  }
  ?>
</ul>

