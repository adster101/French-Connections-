<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');

// $displayData is passed into the layout from our template
$progress = $displayData['progress'];


$ignore = array('review'=>0,'expiry_date'=>1,'ordering'=>0,'changeover_day'=>1,'base_currency'=>1,'tariff_based_on'=>1);

$notices = array();
$percentage = 0;
// If progress not empty - we at least have a property record
// If first unit_id is empty and unit count is 1 - no unit details stored
// If we have one unit and a unit_id then check the images etc
// Determine from above overall progress...
// If property has an expiry date and review is 1 then need to submit for PFR
// Otherwise property is new and needs to be published. Will it go through same screen? Different messaging?
?>

<div class="row-fluid">
  <div class="span8">
    <?php if (empty($progress)) : // If progress empty - brand new propery with no persistent data   ?>
      <?php
      $notices[] = JText::_('COM_HELLOWORLD_LISTING_COMPLETE_PLEASE_COMPLETE_LOCATION_DETAILS');
      ?>
    <?php elseif (!empty($progress) && empty($progress[0]->unit_id)) : // Listing has been created but no unit   ?>
      <?php
      $notices[] = JText::_('COM_HELLOWORLD_LISTING_COMPLETE_PLEASE_COMPLETE_ACCOMMODATION_DETAILS');
      $percentage = 20;
      ?>
    <?php elseif (!empty($progress) && !empty($progress[0]->unit_id) && count($progress) >= 1) : ?>
      <?php $percentage = 40; ?>
      <?php foreach ($progress as $key => $unit) : ?>
        <?php foreach ($unit as $section => $complete) : ?>
          <?php if (array_key_exists($section,$ignore)) { continue; }?>
          <?php if (!$complete) : ?>
            <?php $notices[] = JText::sprintf('COM_HELLOWORLD_HELLOWORLD_LISTING_PROGRESS_NOTICES', $section, $unit->unit_title); ?>
          <?php endif; ?>

        <?php endforeach; ?>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($notices)) : ?>
      <div class="alert alert-info">
        <h4>Property Progress</h4>

        <ul>
          <?php foreach ($notices as $key => $value) : ?>
            <li>
              <?php echo $value; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
  <div class="span4">
    <p>Your listing is <strong><?php echo $percentage ?>%</strong> complete</p>
    <div class="progress progress-striped">
      <div class="bar" style="width: <?php echo $percentage . '%' ?>;"></div>
    </div>
    <p>
      <i class="icon icon-warning"> </i>
      Please complete &nbsp;&nbsp;

      <i class="icon icon-publish"></i>
      Section complete
    </p>
  </div>
</div>
<?php if (empty($notices) && $progress[0]->review) : ?>
  <button class="btn">You done, submit it baby!</button>
<?php endif; ?>






