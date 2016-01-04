<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Below will be useful to be able to use the same params for enquiries as well as reviews
// $cparams = JComponentHelper::getParams('com_media');

$app = JFactory::getApplication();

//$id = $this->state->get('property.id');

$doc = JDocument::getInstance();

// Include the JDocumentRendererMessage class file
require_once JPATH_ROOT . '/libraries/joomla/document/html/renderer/message.php';
$render = new JDocumentRendererMessage($doc);

$id = $this->item->property_id ? $this->item->property_id : '';
$unit_id = $this->item->unit_id ? $this->item->unit_id : '';

$enquiry_data = $app->getUserState('com_accommodation.atleisure.data');
$errors = $app->getUserState('com_accommodation.enquiry.messages');

// Probably better to do this with a live bookable flag?
$owner = JFactory::getUser($this->item->created_by);

$task = ($owner->username == 'atleisure') ? 'listing.getatleisurebookingsummary' : 'listing.enquiry';
// Get the itemID of the accommodation component.
$Itemid = SearchHelper::getItemid(array('component', 'com_accommodation'));
?>
<form class="form-validate form-vertical" id="rental-contact-form" action="" method="post">
  <?php echo JHtml::_('form.token'); ?>
  <fieldset class="adminform">
    <legend>
      <?php echo $this->escape($this->document->title) ?>
    </legend>

    <div class="alert alert-info">
      <p>
        <span class="glyphicon glyphicon-lock"></span>
        Your payment information is encrypted for secure processing
      </p>
    </div>

    <div class="row form-group">

      <div class="col-lg-6">
        <?php echo $this->form->getLabel('CardNumber'); ?>
        <?php echo $this->form->getInput('CardNumber'); ?>
      </div>
      <div class="col-lg-6">
        <?php echo $this->form->getLabel('CardholderName'); ?>
        <?php echo $this->form->getInput('CardholderName'); ?>
      </div>
    </div>
    <div class="row form-group">
      <div class="col-lg-6">
        <?php echo $this->form->getLabel('ExpiryDate'); ?>
        <?php echo $this->form->getInput('ExpiryDate'); ?>
      </div>
      <div class="col-lg-6">
        <?php echo $this->form->getLabel('CVC'); ?>
        <?php echo $this->form->getInput('CVC'); ?>
      </div>
    </div>

    <?php foreach ($this->form->getFieldset('enquiry') as $field): ?>
        <?php if ($field->hidden): ?>
            <?php echo $field->input; ?>
        <?php endif; ?>
    <?php endforeach; ?>
  </fieldset>

  <button type="submit" class="btn btn-danger btn-block" id="enquiry">
    <span class="glyphicon glyphicon-arrow-right"></span>

    <?php echo JText::_('COM_ACCOMMODATION_BOOKING_SUBMIT_PAYMENT'); ?>
  </button>
  <input type="hidden" name="option" value="com_accommodation" />
  <input type="hidden" name="task" value="listing.processatleisurebooking" />
  <input type="hidden" name="next" value="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . '&view=atleisure&layout=confirmation'); ?>" />

</form>
