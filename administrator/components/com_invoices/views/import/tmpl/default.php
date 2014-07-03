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
<form action="<?php echo JRoute::_('index.php?option=com_invoices'); ?>" method="post" name="adminForm" id="adminForm">
  <div id="j-main-container">
    <?php foreach ($fieldsets as $fieldset) : ?>
      <fieldset class="form-inline">
        <legend>
          <?php echo JText::_($fieldset->label); ?>
        </legend>
        <?php foreach ($this->form->getFieldSet($fieldset->name) as $field) : ?>
          <?php echo $field->label; ?> 
          <?php echo $field->input; ?>
        <?php endforeach; ?>
      </fieldset>
    <?php endforeach; ?>
  </div>
  <?php echo JHtml::_('form.token'); ?>
  <input type="hidden" name="task" value="" />
</form>


