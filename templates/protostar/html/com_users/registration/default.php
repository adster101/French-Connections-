<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
?>
<div class="registration<?php echo $this->pageclass_sfx ?>">
  <?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
      <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    </div>
  <?php endif; ?>

  <form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
    <?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one. ?>
      <?php $fields = $this->form->getFieldset($fieldset->name); ?>
      <?php if (count($fields)): ?>
        <fieldset>
          <?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.  ?>
            <legend><?php echo JText::_($fieldset->label); ?></legend>
          <?php endif; ?>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('name'); ?>
             
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('name'); ?>
            </div>
          </div>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('username'); ?>
             
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('username'); ?>
            </div>
          </div>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('password1'); ?>
             
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('password2'); ?>
            </div>
          </div>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('password2'); ?>
             
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('password2'); ?>
            </div>
          </div>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('email1'); ?>
             
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('email1'); ?>
            </div>
          </div>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('email2'); ?>
             
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('email2'); ?>
            </div>
          </div>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('email2'); ?>
             
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('email2'); ?>
            </div>
          </div>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('phone_1'); ?>
             
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('phone_1'); ?>
            </div>
          </div>
        </fieldset>
      <?php endif; ?>
    <?php endforeach; ?>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary validate"><?php echo JText::_('JREGISTER'); ?></button>
      <a class="btn" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
      <input type="hidden" name="option" value="com_users" />
      <input type="hidden" name="task" value="registration.register" />
      <?php echo JHtml::_('form.token'); ?>
    </div>
    <?php foreach ($fields as $field) :// Iterate through the fields in the set and display them. ?>
      <?php if ($field->hidden):// If the field is hidden, just display the input.?>
        <?php echo $field->input; ?>
      <?php endif; ?>
    <?php endforeach; ?>   


  </form>
</div>
