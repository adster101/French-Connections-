<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

?>


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


<input type="hidden" name="task" value="listing.snooze" />
<?php echo JHtml::_('form.token'); ?>
<div>
</div>

<script>

</script>
