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
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_BOOKING_YOUR_DETAILS_LINE'); ?>
    </p>
    <div class="row form-group">
      <div class="col-lg-6">
        <div class="stacked">
          <?php echo $this->form->getLabel('guest_surname'); ?>
          <?php echo $this->form->getInput('guest_surname'); ?>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="stacked">
          <?php echo $this->form->getLabel('guest_forename'); ?>
          <?php echo $this->form->getInput('guest_forename'); ?>
        </div>
      </div>
    </div>
    <div class="row form-group">
      <div class="col-lg-6">
        <div class="stacked">
          <?php echo $this->form->getLabel('guest_email'); ?>
          <?php echo $this->form->getInput('guest_email'); ?>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="stacked">
          <?php echo $this->form->getLabel('guest_phone'); ?>
          <?php echo $this->form->getInput('guest_phone'); ?>
        </div>
      </div>
    </div> 
  </fieldset>  
  <hr />
  <fieldset>
    <legend>
      <?php echo JText::_('COM_ACCOMMODATION_BOOKING_TERMS_AND_CONDITIONS'); ?>
    </legend>

    <p>
      <?php echo JText::_('COM_ACCOMMODATION_BOOKING_TERMS_AND_CONDITIONS_TEXT'); ?>
    </p>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_BOOKING_BELVILLA_TERMS_AND_CONDITIONS'); ?>
    </p>
    <div class="form-inline">
      <?php foreach ($this->form->getFieldset('tos') as $field): ?>
          <?php echo $field->label; ?>
          <?php echo $field->input; ?>
      <?php endforeach; ?>   
    </div>
    <hr />
    <p><?php echo JText::_('COM_ACCOMMODATION_BOOKING_FC_TERMS_AND_CONDITIONS_AGREE'); ?></p>
    <hr />
    <?php foreach ($this->form->getFieldset('enquiry') as $field): ?>
        <?php if ($field->hidden): ?>
            <?php echo $field->input; ?>
        <?php endif; ?>
    <?php endforeach; ?>
  </fieldset>

  <button type="submit" class="btn btn-danger btn-block" id="enquiry">
    <span class="glyphicon glyphicon-arrow-right"></span>

    <?php echo JText::_('COM_ACCOMMODATION_BOOKING_YOUR_DETAILS_PROCEED'); ?>
  </button>
  <input type="hidden" name="option" value="com_accommodation" />
  <input type="hidden" name="task" value="listing.processatleisurebooking" />
  <input type="hidden" name="next" value="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . '&view=atleisure&layout=payment'); ?>" />
</form>
