<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JHtml::_('behavior.formvalidator');

?>

<form action="<?php echo JRoute::_('index.php?option=com_realestate&view=admin&id=' . (int) $this->id) ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
  <?php foreach ($this->form->getFieldsets() as $fieldset) : ?> 
    <fieldset class="panelform">
      <legend>
        <?php echo JText::_($fieldset->label) ?>
      </legend>
      <?php foreach ($this->form->getFieldset($fieldset->name) as $field) : ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </fieldset>
  <?php endforeach; ?>
  <?php echo $this->form->getField('id')->input ?>
  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>
