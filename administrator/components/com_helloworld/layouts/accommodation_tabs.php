<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');$languages = HelloWorldHelper::getLanguages();
$listing_id = $input->get('parent_id','','int');

$lang = HelloWorldHelper::getLang();

// Take the data from the object passed into the template...
$data = $displayData;

// Process the units into a keyed array (useful so we can get at individual units)
// Easier to just foreach ?
$units = HelloWorldHelper::getUnitsById($data['progress']);

// Determine the unit id, if a new unit unit_id = 0 - the listing id is then used as parent in the create unit view
($view == 'propertyversions') ? $unit_id = key($units) : $unit_id = $input->get('unit_id', '0', 'int');

// Set the item which is used below to output the tabs
$item = (!empty($unit_id)) ? $units[$unit_id] : HelloWorldHelper::getEmptyUnit($listing_id);


?>
<ul class="nav nav-tabs">
  <li<?php echo ($view == 'propertyversions') ? ' class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'propertyversions','edit','compass', 'COM_HELLOWORLD_HELLOWORLD_PROPERTY_DETAILS', $item,'parent_id','') ?>
  </li>
  <?php if (count($data['progress']) > 1) : ?>
    <li class="dropdown">
      <a class="dropdown-toggle"
         data-toggle="dropdown"
         href="#">
        Switch to unit
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <?php foreach ($data['progress'] as $value) : ?>
          <li>
            <a href="<?php echo JText::_('index.php?option=com_helloworld&task=unitversions.edit&unit_id=' . $value->unit_id) ?>">
              <?php echo $value->unit_title; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </li>
  <?php endif; ?>
  <li<?php echo ($view == 'unitversions') ? ' class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'unitversions', 'edit', 'home', 'COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id','') ?>
  </li>
  <li<?php echo ($view == 'images' || $view == 'image') ? ' class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'images', 'manage','pictures', 'IMAGE_GALLERY', $item, 'unit_id','') ?>
  </li>
  <li <?php echo ($view == 'availability') ? 'class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'availability', 'manage', 'calendar', 'COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY', $item, 'unit_id') ?>
  </li>
  <li>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'tariffs', 'edit', 'briefcase', 'COM_HELLOWORLD_SUBMENU_MANAGE_TARIFFS', $item, 'unit_id') ?>
  </li>
</ul>