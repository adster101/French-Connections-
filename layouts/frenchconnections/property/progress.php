<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get the input data
$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view', '', 'string');

// $displayData is passed into the layout from our template
$progress = $displayData['status'];
$form = (!empty($displayData['form'])) ? $displayData['form'] : '';
?>

<!--<div class="row-fluid">
  <div class="span9">-->
<?php if ($view == 'listing' && $progress->review == 1 && $progress->complete) : //No notices, listing view for a property that needs review      ?>
  <div class="well well-small">
    <?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_SUBMISSION_BLURB'); ?>
    <hr />
    <fieldset class="panelform">
      <div class="control-group">   
        <?php echo $form->getLabel('admin_notes'); ?>
        <div class="controls">   
          <?php echo $form->getInput('admin_notes'); ?>
        </div>
      </div>      
      <?php echo $form->getInput('tos'); ?>  
      <?php echo $form->getLabel('tos'); ?>
      <hr />
    </fieldset>
    <button class="btn btn-primary" onclick="Joomla.submitbutton('listing.submit')">
      <?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_SUBMIT_FOR_REVIEW_BUTTON'); ?>
    </button>
  </div>
<?php elseif ($view == 'listing' && !$progress->review && $progress->days_to_renewal >= 7 && $progress->complete) : ?>
  <?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_BLURB'); ?>
<?php elseif ($progress->days_to_renewal <= 7 && !$progress->review && !empty($progress->expiry_date)) : ?>
  <div class="alert alert-warning">       
    <h4>Listing Status</h4>
    <p><?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_RENEW_NOW'); ?></p>
    <?php echo JHtml::_('property.renewalButton', $progress->days_to_renewal, $progress->id); ?>
  </div>
<?php elseif ($progress->days_to_renewal < 0 && !empty($progress->expiry_date) && $progress->review < 2) : ?>
  <div class="alert alert-danger">       
    <h4>Listing Status</h4>
    <p><?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_EXPIRED'); ?></p>
    <?php echo JHtml::_('property.renewalButton', $progress->days_to_renewal, $progress->id); ?>
  </div>    
<?php elseif ($progress->review == 1 && $progress->complete) : ?>
  <div class="alert alert-info">
    <h4>Listing Status</h4>
    <p><?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_UNSUBMITTED_CHANGES'); ?></p>
    <a href="<?php echo JRoute::_('index.php?option=com_rental&view=listing&id=' . (int) $progress->id) ?>" class="btn btn-primary">
      <?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_SUBMIT_FOR_REVIEW_BUTTON'); ?>
      <i class="icon icon-arrow-right-2 icon-white"> </i>
    </a>
  </div>
<?php elseif ($progress->review == 2) : ?>
  <div class="alert alert-info">
    <h4>Listing status</h4>
    <p><?php echo JText::_('COM_RENTAL_LISTING_SUBMITTED_FOR_REVIEW'); ?></p>
    <?php
    // Instantiate a new JLayoutFile instance and render the layout
    $layout = new JLayoutFile('joomla.toolbar.standard');

    $options = array(
        'text' => JText::_('COM_RENTAL_LISTING_APPROVE_CHANGES'),
        'doTask' => "Joomla.submitbutton('listing.review')",
        'btnClass' => 'btn btn-primary',
        'class' => 'icon icon-chevron-right');
    echo $layout->render($options);
    ?>

  </div>
<?php endif; ?>
