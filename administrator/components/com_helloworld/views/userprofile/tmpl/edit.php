n<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld') ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
        <?php foreach ($fieldsets as $fieldset) : ?>
          <fieldset>
            <legend><?php echo JText::_($fieldset->label); ?></legend>
            <p><?php echo JText::_($fieldset->description); ?></p>
            <?php foreach ($this->form->getFieldset($fieldset->name) as $field) : ?>
              <div class="control-group">
                <?php echo $field->label; ?>
                <div class="controls">
                  <?php echo $field->input; ?>
                </div>
              </div>
              <hr />
            <?php endforeach; ?>
            <?php echo JHtmlProperty::button('btn btn-primary btn-large pull-right', 'renewal.validateuserdetails', 'icon-next', 'Proceed'); ?>
          </fieldset>
        <?php endforeach; ?>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>


      </form>