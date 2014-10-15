<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');

// Take the data from the object passed into the template...
$data = $displayData['status'];

$class = (empty($data->expiry_date)) ? 'nav nav-wizard clearfix' : 'nav nav-tabs';
?>
<ul class="<?php echo $class ?>">
  <?php
  echo JHtml::_('property.progressTab', $data->property_detail, true, 'COM_REALESTATE_PROPERTY_DETAIL', 'index.php?option=com_realestate&task=propertyversions.edit&realestate_property_id=' . (int) $data->id, 'WOOT WOOT', $view, 'propertyversions');
  echo JHtml::_('property.progressTab', $data->gallery, $data->property_detail, 'IMAGE_GALLERY', 'index.php?option=com_realestate&task=images.manage&realestate_property_id=' . (int) $data->id, 'Wooty woot', $view, 'images');
  if (empty($data->expiry_date))
  {
    echo JHtml::_('property.progressTab', $data->payment, $data->complete, 'PAYMENT', 'index.php?option=com_realestate&task=payment.summary&realestate_property_id=' . (int) $data->id, 'Wooty woot', $view, 'payment');
  }
  ?>
</ul>

