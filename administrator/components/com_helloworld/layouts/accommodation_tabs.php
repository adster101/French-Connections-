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
?>

<?php if ($view == 'property') : ?>
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
<?php elseif ($listing->updated == 1) : ?>
  <div class="alert alert-info">
    <?php echo JText::_('COM_HELLOWORLD_PLEASE_SUBMIT_PROPERTY_FOR_REVIEW'); ?>  
    <a href="" class="btn btn-info">
      <?php echo JText::_('SUBMIT_FOR_REVIEW'); ?>    
    </a>
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
    </a>
  </li>
  <li <?php echo ($view == 'unit') ? 'class=\'active\'' : '' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit') ?>">
      <?php echo JText::_('Accommodation'); ?>
      <i class='icon icon-warning'></i>
    </a>
  </li>
  <li <?php echo ($view == 'images' || $view == 'image') ? 'class=\'active\'' : '' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&id=' . $units[$default_unit]->id . '&listing_id=' . $listing_id) ?>">
      <?php echo JText::_('IMAGE_GALLERY'); ?>
      <?php echo ($units[$default_unit]->images) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
    </a>
  </li>
  <li <?php echo ($view == 'availability') ? 'class=\'active\'' : '' ?>>

    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=availability.edit&id=' . $units[$default_unit]->id) ?>">
      <?php echo JText::_('Availability'); ?>
      <?php echo ($units[$default_unit]->images) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
    </a>     

  </li>
  <li>

    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=tariffs.edit&id=' . $units[$default_unit]->id) ?>">
      <?php echo JText::_('Tariffs'); ?>
      <?php echo ($units[$default_unit]->tariffs) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
    </a>     

  </li>
  <!--<li class="active pull-right" dir="ltr">
    <span class="language">
  <?php //echo JText::_('COM_HELLOWORLD_YOU_ARE_EDITING_IN');  ?>
    </span>
  <?php //echo JHTML::_('select.genericlist', $languages, 'Language', 'onchange="submitbutton(\'changeLanguage\')"', 'value', 'text', $lang);  ?>
  </li>-->
</ul> 