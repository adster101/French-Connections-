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

// Get the id of the item the user is editing
$id = $input->get('id', '', 'int');

$units = $data['units'];

$property = $data['property'];

?>

<?php if ($view == 'property' && $property->review) : ?>
  <div class="alert alert-info">
    <?php //echo JText::_('COM_HELLOWORLD_PLEASE_COMPLETE_LISTING_DETAILS'); ?>
    This listing has unpublished changes. These changes will not appear on your live listing until they have been submitted for review.
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
        <?php foreach ($data['units'] as $value) : ?>
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
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=property.edit&id=' . (int) $data['id']) ?>">
      <i class="icon icon-compass"></i>
      <?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_LISTING_DETAILS') ?>
    </a>
  </li>
  <li <?php echo ($view == 'unit') ? 'class=\'active\'' : '' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit') ?>">
      <i class="icon icon-home"></i>
      <?php echo JText::_('Accommodation'); ?>
    </a>
  </li>
  <li <?php echo ($view == 'images' || $view == 'image') ? 'class=\'active\'' : '' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=images') ?>">
      <i class="icon icon-pictures"></i>
      <?php echo JText::_('IMAGE_GALLERY'); ?>
    </a>
  </li>
  <li <?php echo ($view == 'availability') ? 'class=\'active\'' : '' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=availability.edit') ?>">
      <i class="icon icon-calendar"></i>
      <?php echo JText::_('Availability'); ?>
    </a>
  </li>
  <li>
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=tariffs.edit') ?>">
      <i class="icon icon-briefcase"></i>
      <?php echo JText::_('Tariffs'); ?>
    </a>
  </li>
  <!--<li class="active pull-right" dir="ltr">
    <span class="language">
  <?php //echo JText::_('COM_HELLOWORLD_YOU_ARE_EDITING_IN');  ?>
    </span>
  <?php //echo JHTML::_('select.genericlist', $languages, 'Language', 'onchange="submitbutton(\'changeLanguage\')"', 'value', 'text', $lang);  ?>
  </li>-->
</ul>