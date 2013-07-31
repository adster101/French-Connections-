<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');
$message = '';

// $displayData is passed into the layout from our template
$progress = $displayData['progress'];
$form = (!empty($displayData['form'])) ? $displayData['form'] : '';

$notices = HelloWorldHelper::getProgressNotices($progress); // Get an array of what units still need relevant data added...

if (!empty($progress)) {
  $id = ($progress[0]->id) ? $progress[0]->id : ''; // Id is the main property reference number

  $review = ($progress[0]->review) ? $progress[0]->review : ''; // $review inicates whether the main property listing has been flagged as needing a review
  // $expiry_date - the expiry date of this property
  $expiry_date = ($progress[0]->expiry_date) ? $progress[0]->expiry_date : '';
  $days_to_renewal = HelloWorldHelper::getDaysToExpiry($expiry_date);
}
?>

<div class="row-fluid">
  <div class="span9">
    <?php if (!empty($notices) || empty($progress[0]->title)) : ?>
      <div class="alert alert-info">
        <h4>Listing Progress</h4>
        <ul>
          <?php if (empty($progress[0]->title)) : ?>
            <li>
              <?php echo JText::_('COM_HELLOWORLD_LISTING_COMPLETE_PLEASE_COMPLETE_LOCATION_DETAILS'); ?>
            </li>
          <?php endif; ?>

          <?php foreach ($notices as $key => $value) : ?>
            <li>
              <?php foreach ($notices[$key] as $units) : ?>
                <?php $units = implode(', ', $units); ?>
                <?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_LISTING_PROGRESS_NOTICES', $key, $units); ?>
              <?php endforeach; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php elseif ((empty($notices) && $view == 'listing') && $review) : ?>
      <div class="well well-small">
        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_SUBMISSION_BLURB'); ?>
        <hr />
        <fieldset class="panelform">
          <?php echo $form->getLabel('admin_notes'); ?>
          <?php echo $form->getInput('admin_notes'); ?>

          <?php echo $form->getInput('tos'); ?>
          <?php echo $form->getInput('id'); ?>
        </fieldset>
        <button class="btn btn-primary" onclick="Joomla.submitbutton('listing.submit')">
          <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_SUBMIT_FOR_REVIEW_BUTTON'); ?>
        </button>
      </div>
    <?php elseif (empty($notices) && $view == 'listing' && !$review && $days_to_renewal >= 7) : ?>
      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_BLURB'); ?>
    <?php elseif (empty($notices) && !$review && $days_to_renewal >= 7) : ?>
      <div class="alert alert-notice">
        <h4>Listing Status</h4>
        <p><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_OKAY'); ?></p>
      </div>
    <?php elseif (empty($notices) && $days_to_renewal <= 7 && !$review) : ?>
      <div class="alert alert-danger">
        <h4>Listing Status</h4>
        <p><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_RENEW_NOW'); ?></p>
        <?php echo JHtml::_('property.renewalButton', $days_to_renewal, $id); ?>
      </div>
    <?php elseif ($review) : ?>
      <div class="alert alert-info">
        <h4>Listing Status</h4>
        <p><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_UNSUBMITTED_CHANGES'); ?></p>
        <a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=listing&id=' . (int) $id) ?>" class="btn btn-primary">
          <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_SUBMIT_FOR_REVIEW_BUTTON'); ?>
          <i class="icon icon-arrow-right-2 icon-white"> </i>
        </a>
      </div>
    <?php endif; ?>
  </div>
  <?php // Need to put the following into language strings    ?>
  <div class="span3">
    <h4>Key</h4>
    <p>
      <i class="icon icon-warning"> </i>
      Please complete &nbsp;&nbsp;
    <br /><hr/>

    <i class="icon icon-publish"></i>
    Section complete
    </p>
  </div>
</div>
