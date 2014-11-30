<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_attributes&view=attributetype&layout=edit&id=' . (int) $this->item->id); ?>" id="adminForm" method="post" name="adminForm">

  <div id="j-sidebar-container" class="span2">
    <p>
      <?php
      //echo JText::_('COM_RENTAL_YOU_ARE_EDITING_IN') . '<strong>&nbsp;' . $this->lang . '</strong>';
      //JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
      echo JHTML::_('select.genericlist', JHtml::_('contentlanguage.existing', true, true), 'Language', 'onchange="submitbutton(\'attributetype.language\')"', 'value', 'text', $this->lang);
      ?>
    </p>
  </div>
  <div id="j-main-container" class="span10">

    <fieldset class="adminform">
      <legend><?php echo JText::_('Attribute detail'); ?></legend>
      <?php foreach ($this->form->getFieldset('attribute-type') as $field): ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>         
      <?php endforeach; ?>
    </fieldset>
  </div>
</div>
<input type="hidden" name="task" value="attribute.edit" />

<?php echo JHtml::_('form.token'); ?>
</form>
