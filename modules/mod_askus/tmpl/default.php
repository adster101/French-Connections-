<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<p><?php echo JText::_('MOD_ASKUS_GOT_A_QUESTION_BLURB') ?>
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
<form id="contact-form" action="<?php echo JRoute::_('/contact-us?askus=true'); ?>" method="post" class="form-horizontal">
  <legend><?php echo JText::_('COM_FCCONTACT_CONTACT_US'); ?></legend>
  <fieldset class="adminform">
    <div class="form-group">
      <?php echo $form->getLabel('name'); ?>
      <div class="col-sm-9">
        <?php echo $form->getInput('name'); ?>
      </div>
    </div>
    <div class="form-group">
      <?php echo $form->getLabel('email'); ?> 
      <div class="col-sm-9">
        <?php echo $form->getInput('email'); ?>
      </div>
    </div>
    <div class="form-group">
      <?php echo $form->getLabel('tel'); ?> 
      <div class="col-sm-9">
        <?php echo $form->getInput('tel'); ?>
      </div>
    </div>
   <div class="form-group">
      <?php echo $form->getLabel('message'); ?> 
      <div class="col-sm-9">
        <?php echo $form->getInput('message'); ?>
      </div>
    </div>
    <!-- <div class="form-group">
    <?php echo $form->getLabel('phone'); ?>
    <?php echo $form->getInput('dialling_code'); ?>
    <?php echo $form->getInput('phone_1'); ?>
        </div>-->

   
    <div class="col-sm-9 col-sm-offset-3">
      <button class="btn btn-primary btn-lg btn-block " type="submit">
        <?php echo JText::_('COM_FCCONTACT_SEND_MESSAGE'); ?>
      </button>
      <input type="hidden" name="task" value="contact.send" />
      <?php echo JHtml::_('form.token'); ?>
    </div>
  </fieldset>
</form>
