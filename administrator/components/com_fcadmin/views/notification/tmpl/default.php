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

$fieldsets = $this->form->getFieldSets();
?>
<form action="<?php echo JRoute::_('index.php?option=com_fcadmin&view=notification'); ?>" method="post" name="invoiceForm" id="adminForm" class="form-horizontal clearfix">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container" class="span12">
      <?php endif; ?> 
      <?php foreach ($fieldsets as $fieldset) : ?>
        <fieldset class="form-inline">
          <legend>
            <?php echo JText::_($fieldset->label); ?>
          </legend>
          <?php foreach ($this->form->getFieldSet($fieldset->name) as $field) : ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </fieldset>
      <?php endforeach; ?>   
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>


