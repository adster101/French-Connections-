<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');


?>

<h1>
  <?php echo $this->document->title; ?>
</h1>

<?php if (isset($this->error)) : ?>
  <div class="contact-error">
    <?php echo $this->error; ?>
  </div>
<?php endif; ?>

<form id="contact-form" action="<?php echo JRoute::_('index.php?option=com_registerowner'); ?>" method="post" class="form-validate form-horizontal">
  <legend><?php echo JText::_('COM_REGISTER_OWNER_LEGEND'); ?></legend>
  <fieldset class="adminform">
    <div class="form-group">
      <?php echo $this->form->getLabel('firstname'); ?>
      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-8">
        <?php echo $this->form->getInput('firstname'); ?>
      </div>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('surname'); ?>
      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-8">
        <?php echo $this->form->getInput('surname'); ?>
      </div>
    </div>    

    <div class="form-group">
      <?php echo $this->form->getLabel('email1'); ?>
      <div class="col-md-5 col-lg-6 col-xs-12 col-sm-8">
        <?php echo $this->form->getInput('email1'); ?>
      </div>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('email2'); ?>
      <div class="col-md-5 col-lg-6 col-xs-10 col-xs-8">
        <?php echo $this->form->getInput('email2'); ?>
      </div>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('phone_1'); ?>
      <div class="col-md-4 col-lg-3 col-xs-12 col-sm-8">
        <?php echo $this->form->getInput('dialling_code'); ?>
      </div> 
      <div class="col-md-4 col-lg-5 col-xs-12 col-sm-8">
        <?php echo $this->form->getInput('phone_1'); ?>
      </div>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('where_heard'); ?>
      <div class="col-md-6 col-lg-4 col-xs-12 col-sm-8">
        <?php echo $this->form->getInput('where_heard'); ?>
      </div>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('captcha'); ?>
      <div class="col-md-8 col-lg-9 col-sm-8">
        <?php echo $this->form->getInput('captcha'); ?>
      </div>
    </div>
    <div class="form-actions">
      <button class="btn btn-primary btn-large " type="submit">
        <?php echo JText::_('JSUBMIT'); ?>
      </button>
      <input type="hidden" name="option" value="com_registerowner" />
      <input type="hidden" name="task" value="registerowner.register" />
      <?php echo JHtml::_('form.token'); ?>
    </div>
    <hr />
    <div class="alert alert-info">
      <?php echo Jtext::_('COM_REGISTER_OWNER_ACCEPT_TOS_2'); ?>
    </div>
  </fieldset>
</form>


<div id="modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Terms and Conditions</h3>
  </div>
  <div class="modal-body">

  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>