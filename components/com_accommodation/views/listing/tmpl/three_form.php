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
?>

<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$app = JFactory::getApplication();

$id = $this->item->property_id ? $this->item->property_id : '';
$unit_id = $this->item->unit_id ? $this->item->unit_id : '';

$errors = $app->getUserState('com_accommodation.enquiry.messages');

// Probably better to do this with a live bookable flag?
$owner = JFactory::getUser($this->item->created_by);

$task = ($owner->username == 'atleisure') ? 'listing.bookatleisure' : 'listing.enquiry';
?>

<?php if (count($errors > 0)) : ?>

  <div class="contact-error">
    <?php echo $render->render($errors); ?>
  </div>

<?php endif; ?>

<form class="form-validate" id="contact-form" action="" method="post">
  <?php echo JHtml::_('form.token'); ?>

  <fieldset class="adminform">
    <div class="form-group">
      <?php echo $this->form->getLabel('guest_forename'); ?>
      <?php echo $this->form->getInput('guest_forename'); ?>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('guest_surname'); ?>
      <?php echo $this->form->getInput('guest_surname'); ?>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('guest_email'); ?>
      <?php echo $this->form->getInput('guest_email'); ?>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('guest_phone'); ?>
      <?php echo $this->form->getInput('guest_phone'); ?>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('message'); ?>
      <?php echo $this->form->getInput('message'); ?>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('start_date'); ?>  
      <?php echo $this->form->getInput('start_date'); ?> 
      <span class="input-group-addon"><i class="glyphicon glyphicon-calendar" for="arrival"></i></span>
    </div>
    <div class="form-group">
      <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
        <?php echo $this->form->getLabel('end_date'); ?>  
        <div class="input-group start_date date">
          <?php echo $this->form->getInput('end_date'); ?> 
          <span class="input-group-addon"><i class="glyphicon glyphicon-calendar" for="arrival"></i></span>
        </div>
      </div>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('adults'); ?>
      <?php echo $this->form->getInput('adults'); ?>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('children'); ?>
      <?php echo $this->form->getInput('children'); ?>
    </div>
  </fieldset>

  <div class="form-actions">
    <button class="btn btn-primary btn-large validate" type="submit">
      <?php echo JText::_('COM_ACCOMMODATION_SEND_ENQUIRY'); ?>
    </button>
    <p>
      <a class="btn btn-danger btn-block" id="enquiry" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . $append); ?>#email">
        <?php echo ($this->item->is_bookable) ? JText::_('COM_ACCOMMODATION_SITE_BOOK_NOW') : JText::_('COM_ACCOMMODATION_SITE_CONTACT_OWNER'); ?>  
      </a>
    </p>
    <p>
      <a class="btn btn-warning btn-block" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . $append); ?>#availability">
        <?php echo JText::_('COM_ACCOMMODATION_SITE_CHECK_AVAILABILITY'); ?>  
      </a>
    </p>
    <input type="hidden" name="option" value="com_accommodation" />
    <input type="hidden" name="task" value="<?php echo $task ?>" />
  </div>
</form>



