<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$languages = HelloWorldHelper::getLanguages();
$lang = HelloWorldHelper::getLang();

// Need to take the data from the object passed into the template...
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
$listing = JApplication::getUserState('listing', '');

// Convert the listing detail to an array for easier processing
$listing_details = $listing->getProperties();

// Get the id of the item the user is editing
$id = $input->get('id', '', 'int');

// Get the listing ID
$listing_id = $listing_details['id'];

// Get the units 
$units = $listing->units;

// Test what has been added for the property listing details
$property_details = ($listing_details['id'] && $listing_details['latitude'] && $listing_details['longitude'] && $listing_details['title']) ? true : false;

// Assign a 'default' unit ID 
$units = (!$units) ? array() : $units;
$default_unit = (count($units) > 0) ? key($units) : '';
?>
<?php if ($listing->updated) : ?>
  <div class="alert alert-info">
    <?php echo JText::_('COM_HELLOWORLD_PLEASE_SUBMIT_PROPERTY_FOR_REVIEW'); ?>  
    <a href="" class="btn btn-info">
      <?php echo JText::_('SUBMIT_FOR_REVIEW'); ?>    
    </a>
  </div>
<?php endif; ?>
<?php if ($view == 'property' && !$property_details) : ?>
  <div class="alert alert-info">
    <?php echo JText::_('COM_HELLOWORLD_PLEASE_COMPLETE_LISTING_DETAILS'); ?>  
  </div>
<?php elseif (($view == 'property' && $property_details && empty($units))) : ?>
  <div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo JText::_('COM_HELLOWORLD_LISTING_COMPLETE_PLEASE_COMPLETE_ACCOMMODATION_DETAILS'); ?>
    <a href="index.php?option=com_helloworld&task=unit.edit" class="btn btn-primary">
      <?php echo JText::_('COM_HELLOWORLD_PROCEED'); ?>    
    </a>
  </div>
<?php elseif (($view == 'property' || $view == 'unit') && !empty($units) && !$units[$default_unit]->images) : ?>
  <div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo JText::_('COM_HELLOWORLD_LISTING_COMPLETE_PLEASE_COMPLETE_IMAGES_DETAILS'); ?>
    <a href="index.php?option=com_helloworld&view=images" class="btn btn-primary">
      <?php echo JText::_('COM_HELLOWORLD_PROCEED'); ?>    
    </a>
  </div>
<?php elseif ($view == 'unit') : ?>
  <div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo JText::_('COM_HELLOWORLD_PLEASE_COMPLETE_ACCOMMODATION_DETAILS'); ?>  
  </div>
<?php endif; ?>


<?php if (count($units) > 1) : ?>
  <div class="clearfix">
    <p>You have the following units:&nbsp;</p>
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
  <hr />
<?php endif; ?>
<ul class="nav nav-tabs">
  <li <?php echo ($view == 'property') ? 'class=\'active\'' : '' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=property.edit&id=' . (int) $listing_details['id']) ?>">
      <?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_LISTING_DETAILS') ?>
      <?php if ($property_details) : ?>
        <i class="icon icon-ok"></i>
      <?php else: ?>
        <i class="icon icon-warning"></i>
      <?php endif; ?>
    </a>
  </li>
  <li <?php echo ($view == 'unit') ? 'class=\'active\'' : '' ?>>
    <?php if (!empty($units)) : // This listing has one or more units already     ?> 
      <?php if (count($units) == 1) : // There is only one unit for this listing...so far...  ?>
        <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit&id=' . $units[$default_unit]->id) ?>">
          <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS'); ?>
          <i class='icon icon-ok'></i>
        </a>
      <?php elseif (count($units) > 1) : ?>
        <?php if (array_key_exists($id, $units)) : ?>
          <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit&id=' . $id) ?>">
            <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS'); ?>
            <i class='icon icon-ok'></i>
          </a>
        <?php else: ?>
          <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit&id=' . $units[$default_unit]->id) ?>">
            <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS'); ?>
            <i class='icon icon-ok'></i>
          </a>     
        <?php endif; ?>
      <?php endif; ?>
    <?php elseif (empty($id) && $view == 'unit') : // View is unit, ID is empty, must be a new unit  ?>
      <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit') ?>">
        <?php echo JText::_('Accommodation'); ?>
        <i class='icon icon-warning'></i>
      </a>     
    <?php elseif ($property_details) : // No units supplied, property details complete   ?>
      <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit') ?>">
        <?php echo JText::_('Accommodation'); ?>
        <i class='icon icon-warning'></i>
      </a>
    <?php else: // Brand new property   ?>
      <span class="muted">
        <?php echo JText::_('Accommodation'); ?>
      </span>
    <?php endif; ?>
  </li>
  <li <?php echo ($view == 'images' || $view == 'image') ? 'class=\'active\'' : '' ?>>
    <?php if (!empty($units)) : ?>
      <?php if (count($units) == 1) : // There is only one unit for this listing...so far...  ?>
        <a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&id=' . $units[$default_unit]->id . '&listing_id=' . $listing_id) ?>">
          <?php echo JText::_('IMAGE_GALLERY'); ?>
          <?php echo ($units[$default_unit]->images) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
        </a>
      <?php elseif (count($units) > 1) : ?>
        <?php if (array_key_exists($id, $units)) : // If the  ?>
          <a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&id=' . $units[$id]->id . '&listing_id=' . $listing_id) ?>">
            <?php echo JText::_('IMAGE_GALLERY'); ?>
            <?php echo ($units[$id]->images) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
          </a>
        <?php else: ?>
          <a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&id=' . $units[$default_unit]->id) . '&listing_id=' . $listing_id ?>">
            <?php echo JText::_('IMAGE_GALLERY'); ?>
            <?php echo ($units[$default_unit]->images) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
          </a>     
        <?php endif; ?>
      <?php endif; ?> 
    <?php else: // There are no units so we don't want the user editing this tab ?>
      <span class="muted">
        <?php echo JText::_('IMAGE_GALLERY'); ?>
      </span>
    <?php endif; ?>
  </li>
  <li <?php echo ($view == 'availability') ? 'class=\'active\'' : '' ?>>
    <?php if (!empty($units)) : ?>
      <?php if (count($units) > 0) : ?>
        <?php if (array_key_exists($id, $units)) : ?>
          <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=availability.edit&id=' . $id) ?>">
            <?php echo JText::_('Availability'); ?>
            <?php echo ($units[$id]->availability) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
          </a>
        <?php else: ?>
          <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=availability.edit&id=' . $units[$default_unit]->id) ?>">
            <?php echo JText::_('Availability'); ?>
            <?php echo ($units[$default_unit]->images) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
          </a>     
        <?php endif; ?>
      <?php endif; ?> 
    <?php else: // There are no units so we don't want the user editing this tab ?>
      <span class="muted">
        <?php echo JText::_('Availability'); ?>
      </span>
    <?php endif; ?>
  </li>
  <li>
    <?php if (!empty($units)) : ?>
      <?php if (count($units) > 0) : ?>
        <?php if (array_key_exists($id, $units)) : ?>
          <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=tariffs.edit&id=' . $id) ?>">
            <?php echo JText::_('Tariffs'); ?>
            <?php echo ($units[$id]->availability) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
          </a>
        <?php else: ?>
          <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=tariffs.edit&id=' . $units[$default_unit]->id) ?>">
            <?php echo JText::_('Tariffs'); ?>
            <?php echo ($units[$default_unit]->tariffs) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
          </a>     
        <?php endif; ?>
      <?php endif; ?> 
    <?php else: // There are no units so we don't want the user editing this tab ?>
      <span class="muted">
        <?php echo JText::_('Tariffs'); ?>
      </span>
    <?php endif; ?>
  </li>


  <li class="active pull-right" dir="ltr">
    <span class="language">
      <?php echo JText::_('COM_HELLOWORLD_YOU_ARE_EDITING_IN'); ?>
    </span>
    <?php
    echo JHTML::_('select.genericlist', $languages, 'Language', 'onchange="submitbutton(\'changeLanguage\')"', 'value', 'text', $lang);
    ?>
  </li>
</ul> 