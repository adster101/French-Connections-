<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');

// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();
?>



<form action="<?php echo JRoute::_('index.php?option=com_users&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" enctype="multipart/form-data">

  <?php echo JLayoutHelper::render('joomla.edit.item_title', $this); ?>

  <fieldset>
    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_USERS_USER_ACCOUNT_DETAILS', true)); ?>
    <?php foreach ($this->form->getFieldset('user_details') as $field) : ?>
      <div class="control-group">
        <div class="control-label">
          <?php echo $field->label; ?>
        </div>
        <div class="controls">
          <?php echo $field->input; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'sms', JText::_('COM_ADMIN_VIEW_PROFILE_SMS_SETTINGS', true)); ?>

    <fieldset class="adminform form-horizontal">
      <legend><?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_SMS_CONTACT_DETAILS'); ?></legend> 
      <?php echo $this->form->getLabel('smsprefs'); ?>
      <div class="control-group">
        <?php echo $this->form->getLabel('sms_alert_number'); ?>
        <div class="controls">
          <?php echo $this->form->getInput('sms_alert_number'); ?>
        </div>
      </div>
      <?php if (!$this->item->sms_valid) : ?>
        <div class="control-group">
          <?php echo $this->form->getLabel('dummy_validation_code'); ?>
          <div class="controls">
            <?php echo $this->form->getInput('dummy_validation_code'); ?>
          </div>
        </div>
      <?php elseif ($this->item->sms_valid) : ?>
        <?php echo $this->form->getLabel('sms_valid_message'); ?>
      <?php endif; ?>
      <div class="control-group">
        <?php echo $this->form->getLabel('sms_nightwatchman'); ?>
        <div class="controls">
          <?php echo $this->form->getInput('sms_nightwatchman'); ?>
        </div>
      </div>
    </fieldset> 
    <?php echo JHtml::_('bootstrap.endTabSet'); ?>

    <?php echo $this->form->getInput('sms_valid'); ?>
    <?php echo $this->form->getInput('sms_status'); ?>
    <?php if ($this->grouplist) : ?>
      <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'groups', JText::_('COM_USERS_ASSIGNED_GROUPS', true)); ?>
      <?php echo $this->loadTemplate('groups'); ?>
      <?php echo JHtml::_('bootstrap.endTab'); ?>
    <?php endif; ?>

    <?php
    foreach ($fieldsets as $fieldset) :
      if ($fieldset->name == 'user_details') :
        continue;
      endif;
      ?>
      <?php echo JHtml::_('bootstrap.addTab', 'myTab', $fieldset->name, JText::_($fieldset->label, true)); ?>
      <?php foreach ($this->form->getFieldset($fieldset->name) as $field) : ?>
        <?php if ($field->hidden) : ?>
          <div class="control-group">
            <div class="controls">
              <?php echo $field->input; ?>
            </div>
          </div>
        <?php else: ?>
          <div class="control-group">
            <div class="control-label">
              <?php echo $field->label; ?>
            </div>
            <div class="controls">
              <?php echo $field->input; ?>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
      <?php echo JHtml::_('bootstrap.endTab'); ?>
    <?php endforeach; ?>

    <?php echo JHtml::_('bootstrap.endTabSet'); ?>
  </fieldset>

  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>
