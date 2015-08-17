<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JHtml::_('behavior.formvalidator');
JHtml::_('bootstrap.tooltip');
?>

<h1>
  <?php echo $this->document->title; ?>
</h1>

<?php if (isset($this->error)) : ?>
  <div class="contact-error">
    <?php echo $this->error; ?>
  </div>
<?php endif; ?>

<form id="contact-form" action="<?php echo JRoute::_('index.php?option=com_registerowner&task=registerowner.resetpassword'); ?>" method="post" class="form-validate form-vertical">

  <?php echo $this->form->getLabel('note'); ?>
  
  <fieldset class="adminform">
    <legend><?php echo JText::_('COM_REGISTER_OWNER_RESETPASSWORD_LEGEND'); ?></legend>

    <div class="form-group">
      <?php echo $this->form->getLabel('email'); ?>
      <?php echo $this->form->getInput('email'); ?>
    </div>
    <div class="form-group">
      <?php echo $this->form->getLabel('captcha'); ?>
      <?php echo $this->form->getInput('captcha'); ?>
    </div>
    <div class="form-actions">
      <button class="btn btn-primary btn-large " type="submit">
        <?php echo JText::_('JSUBMIT'); ?>
      </button>
      <input type="hidden" name="option" value="com_registerowner" />
      <input type="hidden" name="task" value="registerowner.resetpassword" />
      <?php echo JHtml::_('form.token'); ?>
    </div>    
  </fieldset>
</form>