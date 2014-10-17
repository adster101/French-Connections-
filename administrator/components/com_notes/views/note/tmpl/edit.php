<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.framework');
JHtml::_('behavior.keepalive');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rental&laytout=modal&view=notes&tmpl=component') ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
  <fieldset class="filter">
    <div id="filter-bar" class="btn-toolbar">
      <a class="btn" href="javascript:history.back();"><i class="icon icon-backward-2"></i>&nbsp;Back</a>     

      <button class="btn btn-primary">
        <i class="icon icon-save icon-white"></i>&nbsp;Save</button>

    </div>
  </fieldset>
  <hr />  
  <?php foreach ($this->form->getFieldSets() as $name => $fieldset): ?>

    <fieldset class="panelform">
      <?php foreach ($this->form->getFieldset() as $field) : ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </fieldset>
  <?php endforeach; ?>
  <input type="hidden" name="task" value="note.save" />
  <?php echo JHtml::_('form.token'); ?>

</form>
