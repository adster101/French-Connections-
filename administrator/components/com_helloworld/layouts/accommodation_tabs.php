<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');
$languages = HelloWorldHelper::getLanguages();

$listing_id = $input->get('property_id', '', 'int');

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
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'propertyversions', 'edit', 'compass', 'COM_HELLOWORLD_HELLOWORLD_PROPERTY_DETAILS', $item, 'property_id', '') ?>
  </li>
  <?php if (count($data['progress']) > 1) : ?>
    <li class="dropdown <?php echo ($view == 'unitversions') ? 'active' : '' ?>">
      <a class="dropdown-toggle"
         data-toggle="dropdown"
         href="#">
        <i class="icon icon-home"></i>
        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS'); ?>
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <?php foreach ($data['progress'] as $value) : ?>
          <li>
            <?php echo JHtmlProperty::progressButton($item->id, $value->unit_id, 'unitversions', 'edit', 'home', $value->unit_title, $units[$value->unit_id], 'unit_id', '') ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </li>
  <?php else: ?>
    <li<?php echo ($view == 'unitversions') ? ' class=\'active\'' : '' ?>>
      <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'unitversions', 'edit', 'home', 'COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id', '') ?>
    </li>
  <?php endif; ?>
  <?php if (count($data['progress']) > 1) : ?>
    <li class="dropdown <?php echo ($view == 'images') ? 'active' : '' ?>">
      <a class="dropdown-toggle"
         data-toggle="dropdown"
         href="#">
        <i class="icon icon-pictures"></i>
        <?php echo JText::_('IMAGE_GALLERY'); ?>
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <?php foreach ($data['progress'] as $value) : ?>
          <li>
            <?php echo JHtmlProperty::progressButton($item->id, $value->unit_id, 'images', 'manage', 'pictures', $value->unit_title, $units[$value->unit_id], 'unit_id', '') ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </li>
  <?php else: ?>
  <li<?php echo ($view == 'images' || $view == 'image') ? ' class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'images', 'manage', 'pictures', 'IMAGE_GALLERY', $item, 'unit_id', '') ?>
  </li>
  <?php endif; ?>
  <?php if (count($data['progress']) > 1) : ?>
    <li class="dropdown <?php echo ($view == 'availability') ? 'active' : '' ?>">
      <a class="dropdown-toggle"
         data-toggle="dropdown"
         href="#">
        <i class="icon icon-calendar-2"></i>
        <?php echo JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY'); ?>
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <?php foreach ($data['progress'] as $value) : ?>
          <li>
            <?php echo JHtmlProperty::progressButton($item->id, $value->unit_id, 'availability', 'manage', 'calendar-2', $value->unit_title, $units[$value->unit_id], 'unit_id') ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </li>
  <?php else: ?>
  <li <?php echo ($view == 'availability') ? 'class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'availability', 'manage', 'calendar', 'COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY', $item, 'unit_id') ?>
  </li>
  <?php endif; ?>
  <?php if (count($data['progress']) > 1) : ?>
    <li class="dropdown <?php echo ($view == 'tariffs') ? 'active' : '' ?>">
      <a class="dropdown-toggle"
         data-toggle="dropdown"
         href="#">
        <i class="icon icon-briefcase"></i>
        <?php echo JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_TARIFFS'); ?>
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <?php foreach ($data['progress'] as $value) : ?>
          <li>
            <?php echo JHtmlProperty::progressButton($item->id, $value->unit_id, 'unitversions', 'tariffs', 'briefcase', $value->unit_title, $units[$value->unit_id], 'unit_id') ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </li>
  <?php else: ?>
  <li>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'unitversions', 'tariffs', 'briefcase', 'COM_HELLOWORLD_SUBMENU_MANAGE_TARIFFS', $item, 'unit_id') ?>
  </li>
  <?php endif; ?>
  <?php if (count($data['progress']) > 1) : ?>
    <li class="dropdown <?php echo ($view == 'reviews') ? 'active' : '' ?>">
      <a class="dropdown-toggle"
         data-toggle="dropdown"
         href="#">
        <i class="icon icon-briefcase"></i>
        <?php echo JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_REVIEWS'); ?>
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <?php foreach ($data['progress'] as $value) : ?>
          <li>
            <?php echo JHtmlProperty::progressButton($item->id, $value->unit_id, 'unitversions', 'reviews', 'comment', $value->unit_title, $units[$value->unit_id], 'unit_id') ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </li>
  <?php else: ?>
  <li <?php echo ($view == 'reviews') ? 'class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'unitversions', 'reviews', 'comment', 'COM_HELLOWORLD_SUBMENU_MANAGE_REVIEWS', $item, 'unit_id') ?>
  </li>
  <?php endif; ?>
</ul>