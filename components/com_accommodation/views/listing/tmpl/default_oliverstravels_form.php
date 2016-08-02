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

// http://scripts.affiliatefuture.com/AFClick.asp?affiliateID=340247&merchantID=6436&programmeID=20995&mediaID=0&tracking=&url=http://www.oliverstravels.com/france/french-riviera-cote-dazur/villa-diamant/
//$next = new JURI('http://scripts.affiliatefuture.com/AFClick.asp');
//$query = array('affiliateID' => 6363, 'programmeID' => 20995, 'url' => $url)
//$next->
?>

<div class="panel panel-default" id="contact">
  <div class="panel-heading">
  <?php if ($this->item->unit_title) : ?>
        <h5>
          <?php echo ($this->item->is_bookable) ? JText::_('COM_ACCOMMODATION_BOOK_THIS_PROPERTY') : JText::_('COM_ACCOMMODATION_EMAIL_THE_OWNER') ?>
        </h5>
    <?php endif; ?>
  </div>
  <div class="panel-body">
    <form class="form-validate form-vertical" id="rental-contact-form" action="" method="post">
      <?php echo JHtml::_('form.token'); ?>
      <fieldset class="adminform">
        <p><?php echo Jtext::_('COM_ACCOMMODATION_AT_LESIURE_TELL_US_YOUR_DATES'); ?></p>
        <?php if (count($errors > 0)) : ?>
          <div class="contact-error">
            <?php echo $render->render($errors); ?>
          </div>
        <?php endif; ?>
        <div class="form-group row">
          <div class="col-lg-6">
            <?php echo $this->form->getLabel('guest_forename'); ?>
            <?php echo $this->form->getInput('guest_forename'); ?>
          </div>
          <div class="col-lg-6">
            <?php echo $this->form->getLabel('guest_surname'); ?>
            <?php echo $this->form->getInput('guest_surname'); ?>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-lg-12">
            <?php echo $this->form->getLabel('guest_email'); ?>
            <?php echo $this->form->getInput('guest_email'); ?>
          </div>
        </div>
      </fieldset>

      <button type="submit" class="btn btn-danger btn-lg btn-block" id="enquiry" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . $append); ?>#email">
        <?php echo ($this->item->is_bookable) ? JText::_('COM_ACCOMMODATION_SITE_SEE_PRICES_AND_BOOK_NOW') : JText::_('COM_ACCOMMODATION_SITE_CONTACT_OWNER'); ?>
      </button>
      <hr />
      <div class="row">
        <div class="col-lg-12 col-sm-12">
          <div class="checkbox">
            <?php echo $this->form->getInput('newsletter_yn'); ?>
          </div>
        </div>
      </div>
      <input type="hidden" name="option" value="com_accommodation" />
      <input type="hidden" name="task" value="listing.enquiry" />
      <input type="hidden" name="next" value="<?php echo $this->item->booking_url ?>" />
    </form>
  </div>
</div>
