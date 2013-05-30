<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$progress = $displayData['progress'];
$review = $progress[0]->review;

$notices = array();

// If progress not empty - we at least have a property record
// If first unit_id is empty and unit count is 1 - no unit details stored
// If we have one unit and a unit_id then check the images etc
// Determine from above overall progress...
// If property has an expiry date and review is 1 then need to submit for PFR
// Otherwise property is new and needs to be published. Will it go through same screen? Different messaging?

?>

<?php if (empty($progress)) : // If progress empty - brand new propery with no persistent data  ?>
  <?php $notices[] = JText::_('COM_HELLOWORLD_LISTING_COMPLETE_PLEASE_COMPLETE_ACCOMMODATION_DETAILS'); ?>
<?php elseif (!empty($progress) && empty($progress[0]->unit_id)) : // Listing has been created but no unit   ?>
  <div class="progress">
    <div class="bar" style="width: 20%;"></div>
  </div>
<?php elseif (!empty($progress) && !empty($progress[0]->unit_id) && count($progress) >= 1) : ?>
  <div class="progress">
    <div class="bar" style="width: 40%;"></div>
  </div>
<?php endif; ?>
 <?php foreach ($progress as $key => $value) : ?>
    <?php foreach ($value as $section => $complete) : ?>
      <?php if (!$complete) : ?>
        <?php $notices[] = JText::sprintf('COM_HELLOWORLD_HELLOWORLD_LISTING_PROGRESS_NOTICES', $section, $key); ?>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endforeach; ?>
<?php if (!empty($notices)) : ?>
  <div class="alert alert-info">
    <h4>Property Progress</h4>
     <div class="progress">
    <div class="bar" style="width: 20%;"></div>
  </div>
    <ul>
      <?php foreach ($notices as $key => $value) : ?>
        <li>
          <?php echo $value; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if (empty($notices) && $progress[0]->review) : ?>
  <button class="btn">You done, submit it baby!</button>
<?php endif; ?>





