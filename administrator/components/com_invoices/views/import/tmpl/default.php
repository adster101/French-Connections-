<?php
/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// no direct access
defined('_JEXEC') or die;
JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));

$user = JFactory::getUser();
$userId = $user->get('id');
$fieldsets = $this->form->getFieldSets();
?>

  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <?php foreach ($fieldsets as $fieldset) : ?>
        <fieldset>
          <legend>
            <?php echo $fieldset->label; ?>
          </legend>
          <?php foreach ($this->form->getFieldSet($fieldset->name) as $field) : ?>
            <?php echo $field->input; ?>
            <?php echo $field->label; ?>
          <?php endforeach; ?>
        </fieldset>
      <?php endforeach; ?>
      <?php echo JToolbar::getInstance('toolbar')->render('toolbar'); ?>
    </div>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />


