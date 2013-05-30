<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$form = $displayData['form'];
$progress = $displayData['progress'];
$notices = array();

?>

<?php if (empty($item->expiry_date)) : ?>
<div class="alert alert-info">
  Property listing 0% complete
</div>
<?php endif; ?>

<?php if (empty($progress['units'])) : // Listing has no units ?>
  <?php $notices[] = JText::_('COM_HELLOWORLD_LISTING_COMPLETE_PLEASE_COMPLETE_ACCOMMODATION_DETAILS'); ?>
<?php elseif (!empty($progress['units'])) : // Listing has unit(s) ?>
  <?php foreach ($progress['units'] as $key => $value) : ?>
    <?php foreach ($value as $section => $complete) : ?>
      <?php if (!$complete) : ?>
        <?php $notices[] = JText::sprintf('COM_HELLOWORLD_HELLOWORLD_LISTING_PROGRESS_NOTICES', $section, $key); ?>
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
<?php elseif (empty($notices) && $progress['review']) : ?>
  <div class="well well-small">
    <form action="<?php echo JRoute::_('index.php?option=com_helloworld&task=propertysubmit&id=' . (int) $progress['listing']); ?>" method="post" name="" id="" class=" ">
      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_SUBMISSION_BLURB'); ?>
      <hr />
      <fieldset class="panelform">
        <?php foreach ($form->getFieldset('submit') as $field): ?>
          <div class="control-group">
            <?php echo $field->label; ?>
            <div class="controls">
              <?php echo $field->input; ?>
            </div>
          </div>
        <?php endforeach; ?>
        <button class="btn btn-primary">
          <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_SUBMIT_FOR_REVIEW_BUTTON'); ?>
        </button>
      </fieldset>
    </form>
  </div>
<?php endif; ?>





