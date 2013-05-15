<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

$languages = HelloWorldHelper::getLanguages();
$lang = HelloWorldHelper::getLang();

// Need to take the data from the object passed into the template...
$data = $displayData;

// Get the input data
$input = $app->input;
$view = $input->get('view', '', 'string');
$unit_id = $input->get('unit_id','','int');
// PRocess the units into associated array

$units = HelloWorldHelper::getUnitsById($data['units']);

$item = $units[$unit_id];

?>
<ul class="nav nav-tabs">
  <li <?php echo ($view == 'property') ? 'class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'property', 'compass', 'COM_HELLOWORLD_HELLOWORLD_PROPERTY_DETAILS', $item) ?>
  </li>
  <li <?php echo ($view == 'unitversions') ? 'class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'unitversions', 'home', 'COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id','') ?>
  </li>
  <li <?php echo ($view == 'images' || $view == 'image') ? 'class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'images', 'pictures', 'IMAGE_GALLERY', $item, 'unit_id','') ?>
  </li>
  <li <?php echo ($view == 'availability') ? 'class=\'active\'' : '' ?>>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'availability', 'calendar', 'COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY', $item, 'unit_id') ?>
  </li>
  <li>
    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'tariffs', 'briefcase', 'COM_HELLOWORLD_SUBMENU_MANAGE_TARIFFS', $item, 'unit_id') ?>
  </li>

  <?php if (count($data['units']) > 1) : ?>
    <li class="dropdown">
      <a class="dropdown-toggle"
         data-toggle="dropdown"
         href="#">
        Switch unit
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <?php foreach ($data['units'] as $value) : ?>
          <li>
            <a href="<?php echo JText::_('index.php?option=com_helloworld&task=unitversions.edit&unit_id=' . $value->unit_id) ?>">
              <?php echo $value->unit_title; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </li>

  <?php endif; ?>
</ul>