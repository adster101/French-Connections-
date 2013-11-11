<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');

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

$preview = ($review) ? '&preview=1' : '';


$link = '/listing/' . (int) $progress[0]->id . '?unit_id=' . (int) $progress[0]->unit_id . $preview;
?>

<div class="row-fluid">
  <div class="span9">
    <?php if (!empty($notices)) : ?>
      <div class="alert alert-info">
        <?php if ($progress[0]->review) : ?>
          <div class="pull-right">
            <a target="_blank" href="<? echo JRoute::_($link); ?>" title="COM_HELLOWORLD_HELLOWORLD_PREVIEW_PROPERTY" class="btn btn-primary">Preview <i class="icon icon-out-2"> </i></a>
          </div>
        <?php endif; ?>
        <h4>Listing Progress</h4>
        <ul>
          <?php if (empty($progress[0]->latitude) && empty($progress[0]->longitude)) : ?>
            <li>
              <?php echo JText::_('COM_HELLOWORLD_LISTING_COMPLETE_PLEASE_COMPLETE_LOCATION_DETAILS'); ?>
            </li>
          <?php endif; ?>

          <?php foreach ($notices as $key => $value) : ?>
            <li>
              <?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_LISTING_PROGRESS_NOTICES', $key); ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php elseif ((empty($notices) && $view == 'listing') && $review) : //No notices, listing view for a property that needs review  ?>
      <div class="well well-small">
        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_SUBMISSION_BLURB'); ?>
        <hr />
        <fieldset class="panelform">
          <div class="control-group">   
            <?php echo $form->getLabel('admin_notes'); ?>
            <div class="controls">   
              <?php echo $form->getInput('admin_notes'); ?>
            </div>
          </div>
          <?php echo $form->getInput('tos'); ?>
          <hr />
        </fieldset>
        <button class="btn btn-primary" onclick="Joomla.submitbutton('listing.submit')">
          <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_SUBMIT_FOR_REVIEW_BUTTON'); ?>
        </button>
      </div>
    <?php elseif (empty($notices) && $view == 'listing' && !$review && $days_to_renewal >= 7) : ?>
      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_BLURB'); ?>
    <?php elseif (empty($notices) && !$review && $days_to_renewal >= 7) : ?>
      <div class="alert alert-info">
          <div class="pull-right">
            <a target="_blank" href="<? echo JRoute::_($link); ?>" title="COM_HELLOWORLD_HELLOWORLD_PREVIEW_PROPERTY" class="btn btn-primary">Preview <i class="icon icon-out-2"> </i></a>
          </div>
        <h4>Listing Status</h4>
        <p><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_OKAY'); ?></p>
      </div>
    <?php elseif (empty($notices) && $days_to_renewal <= 7 && !$review && empty($expiry_date)) : ?>
      <div class="alert alert-danger">
        <?php if ($progress[0]->review) : ?>
          <div class="pull-right">
            <a target="_blank" href="<? echo JRoute::_($link); ?>" title="COM_HELLOWORLD_HELLOWORLD_PREVIEW_PROPERTY" class="btn btn-primary">Preview <i class="icon icon-out-2"> </i></a>
          </div>
        <?php endif; ?>        
        <h4>Listing Status</h4>

        <p><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_RENEW_NOW'); ?></p>
        <?php echo JHtml::_('property.renewalButton', $days_to_renewal, $id); ?>
      </div>
    <?php elseif ($review) : ?>
      <div class="alert alert-info">
        <?php if ($progress[0]->review) : ?>
          <div class="pull-right">
            <a target="_blank" href="<? echo JRoute::_($link); ?>" title="COM_HELLOWORLD_HELLOWORLD_PREVIEW_PROPERTY" class="btn btn-primary">Preview <i class="icon icon-out-2"> </i></a>
          </div>
        <?php endif; ?>
        <h4>Listing Status</h4>
        <p><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_UNSUBMITTED_CHANGES'); ?></p>
        <a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=listing&id=' . (int) $id) ?>" class="btn btn-primary">
          <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_SUBMIT_FOR_REVIEW_BUTTON'); ?>
          <i class="icon icon-arrow-right-2 icon-white"> </i>
        </a>
      </div>
    <?php endif; ?>
  </div>
  <?php // Need to put the following into language strings     ?>
  <div class="span3">
    <h4>Key</h4>
    <span>
      <i class="icon icon-warning"> </i>
      Please complete
    </span>
    <span>

      <i class="icon icon-publish"></i>
      Required fields complete
    </span>

  </div>
</div>
