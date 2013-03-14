<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$data = $displayData;

$app = JFactory::getApplication();

// Get the input data
$input = $app->input;

// Get the user object
$user = JFactory::getUser();

// Get the option
$option = $input->get('option', '', 'string');

// Get the view
$view = $input->get('view', '', 'string');

// Retrieve the listing details from the session
$listing = JApplication::getUserState('listing', 'freckles');

// Convert the listing detail to an array for easier processing
$listing_details = $listing->getProperties();

// Get the id of the item the user is editing
$id = $input->get('id', '', 'int');

$units = $listing_details['units'];
?>

<?php if (count($listing_details['units']) > 1) : ?>
  <div>
    <p>You have the following units:</p>
    <div class="btn-group">
      <a class="btn dropdown-toggle btn-small" data-toggle="dropdown" href="#">
        - Please choose unit to edit -
        <span class="caret"></span>
      </a>
      <ul class="dropdown-menu">
        <?php foreach ($listing_details['units'] as $value) : ?>
          <li>
            <a href="<?php echo JText::_('index.php?option=com_helloworld&task=unit.edit&id=' . $value->id) ?>">
              <?php echo $value->unit_title; ?>
            </a>
          </li>
        <?php endforeach; ?>

      </ul>
    </div>
  </div>
<?php endif; ?>
<ul class="nav nav-tabs">
  <li <?php echo ($view == 'property') ? 'class=\'active\'' : '' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=property.edit&id=' . (int) $listing_details['listing_id']) ?>">
      <?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_LISTING_DETAILS') ?>
      <?php if ($listing_details['listing_id'] && $listing_details['latitude'] && $listing_details['longitude'] && $listing_details['city'] && $listing_details['listing_title']) : ?>
        <i class="icon icon-ok"></i>
      <?php else: ?>
        <i class="icon icon-warning"></i>
      <?php endif; ?>
    </a>
  </li>
  <li <?php echo ($view == 'unit') ? 'class=\'active\'' : '' ?>>
    <?php if (!empty($units)) : ?>
      <?php foreach ($listing_details['units'] as $unit => $detail) : ?>
       <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit&id=' . $detail->id) ?>">
        <?php echo JText::_('Accommodation'); ?>
        <i class='icon icon-warning'></i>
      </a>
        
      <?php endforeach; ?>
    <?php else: // No units supplied, guess it must be a new property ?>
      <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit') ?>">
        <?php echo JText::_('Accommodation'); ?>
        <i class='icon icon-warning'></i>
      </a>
    <?php endif; ?>

  </li>
  <li>
    <a href="#">Image gallery
      <?php //echo (!empty($data['progress']) && $data['progress']['images']) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
    </a>
  </li>
  <li>
    <a href="#">Availability
      <?php echo (!empty($units)) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
    </a>
  </li>
  <li>
    <a href="#">Facilities
      <?php echo (!empty($units)) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>

    </a>
  </li>
  <li>
    <a href="#">Tariffs
      <?php echo (!empty($units)) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>

    </a>
  </li>

</ul>
