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

<div class="panel panel-default" id="contact">
  <?php if ($this->item->unit_title) : ?>
    <div class="panel-heading">
      <h5>
        <?php echo ($this->item->is_bookable) ? JText::_('COM_ACCOMMODATION_BOOK_THIS_PROPERTY') : JText::_('COM_ACCOMMODATION_EMAIL_THE_OWNER') ?>
      </h5> 
    <?php endif; ?>   
  </div>
  <div class="panel-body">
    <form class="form-validate form-vertical small" id="rental-contact-form" action="" method="post">
      <?php echo JHtml::_('form.token'); ?>
      <fieldset class="adminform">
        <div class="form-group row">
          <div class="col-lg-12 col-sm-12">
            <?php echo $this->form->getLabel('guest_forename'); ?>
            <?php echo $this->form->getInput('guest_forename'); ?>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-lg-12 col-sm-12">
            <?php echo $this->form->getLabel('guest_surname'); ?>
            <?php echo $this->form->getInput('guest_surname'); ?>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-lg-12 col-sm-12">
            <?php echo $this->form->getLabel('guest_email'); ?>
            <?php echo $this->form->getInput('guest_email'); ?>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-lg-12 col-sm-12">
            <?php echo $this->form->getLabel('guest_phone'); ?>
            <?php echo $this->form->getInput('guest_phone'); ?>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-lg-12 col-sm-12">
            <?php echo $this->form->getLabel('message'); ?>
            <?php echo $this->form->getInput('message'); ?>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-lg-12 col-sm-12">
            <?php echo $this->form->getLabel('start_date'); ?> 
            <div class="input-group start_date date">
              <?php echo $this->form->getInput('start_date'); ?> 
              <span class="input-group-addon"><i class="glyphicon glyphicon-calendar" for="start_date"></i></span>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-lg-12 col-sm-12">
            <?php echo $this->form->getLabel('end_date'); ?>  
            <div class="input-group end_date date">
              <?php echo $this->form->getInput('end_date'); ?> 
              <span class="input-group-addon"><i class="glyphicon glyphicon-calendar" for="end_date"></i></span>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-lg-6 col-md-6 col-sm-4">
            <?php echo $this->form->getLabel('adults'); ?>
            <?php echo $this->form->getInput('adults'); ?>
          </div>

          <div class="col-lg-6 col-md-6 col-sm-4">

            <?php echo $this->form->getLabel('children'); ?>
            <?php echo $this->form->getInput('children'); ?>
          </div>
        </div>
      </fieldset>

      <button type="submit" class="btn btn-danger btn-lg btn-block" id="enquiry" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . $append); ?>#email">
        <?php echo ($this->item->is_bookable) ? JText::_('COM_ACCOMMODATION_SITE_BOOK_NOW') : JText::_('COM_ACCOMMODATION_SITE_CONTACT_OWNER'); ?>  
      </button>
      <input type="hidden" name="option" value="com_accommodation" />
      <input type="hidden" name="task" value="<?php echo $task ?>" />
    </form>
  </div>
</div>


