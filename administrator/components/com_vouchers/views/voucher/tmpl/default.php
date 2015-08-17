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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');

$user = JFactory::getUser();
$userId = $user->get('id');
?>

<form action="<?php echo JRoute::_('index.php?option=com_vouchers&id=' . (int) $this->item->id) ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

  <div id="j-main-container" class="span10">

    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_VOUCHERS_VOUCHER_DETAIL'); ?></legend>
      <?php foreach ($this->form->getFieldset('voucher') as $field): ?>
      <div class="control-group">
        <?php echo $field->label; ?>
        <div class="controls">
          <?php echo $field->input; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </fieldset>     

  </div>
  <input type="hidden" name="boxchecked" value="0" />

  <?php echo JHtml::_('form.token'); ?>
  <input type="hidden" name="task" value="" />

</form>



