<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.formvalidation');
$fieldsets = $this->form->getFieldSets();
?>
<form class="form-validate form-vertical" action="<?php echo JRoute::_('index.php?option=com_specialoffers&layout=edit&id=' . (int) $this->item->id); ?>" id="adminForm" method="post" name="adminForm">


  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <?php foreach ($fieldsets as $fieldset): ?> 
        <fieldset class="adminform">        
            <legend><?php echo JText::_($fieldset->label); ?></legend>

          <?php foreach ($this->form->getFieldset($fieldset->name) as $field): ?> 

            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>       
          </fieldset>

        <?php endforeach; ?>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<input type="hidden" name="task" value="" />

<?php echo JHtml::_('form.token'); ?>
</form>

