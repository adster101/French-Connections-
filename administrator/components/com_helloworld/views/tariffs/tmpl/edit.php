<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Get all the fieldsets in the tariffs form group
$tariff_field_sets = $this->form->getFieldSets('tariffs');

?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=availability&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="tariffs-form" class="form-validate">
  <div class="width-60 fltlft">
    <fieldset class="adminform">		
      <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_TARIFFS'); ?></legend>
      <?php foreach ($tariff_field_sets as $fieldset) { ?>
        <fieldset class="adminform">
          <table class="adminformlist" id="<?php echo $fieldset->name; ?>">		
            <tr>
              <?php foreach ($this->form->getFieldset($fieldset->name) as $field) { ?>
                <td>
                  <?php
                    echo $field->label;
                    echo $field->input;
                  ?>
                </td> 
              <?php } // End of foreach getFieldSet fieldset name ?>
            </tr>
          </table>			
        </fieldset>
      <?php } // End of foreach tariff field sets ?>
    </fieldset>
  </div>
  
  <div class="width-40 fltrt">
    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ADDITIONAL_TARIFFS_DETAIL'); ?></legend>
      <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset('additional-fields') as $field): ?>
          <li><?php echo $field->label;
        echo $field->input; ?></li>
<?php endforeach; ?>
      </ul>			
    </fieldset>


    <input type="hidden" name="task" value="tariffs.edit" />
<?php echo JHtml::_('form.token'); ?>
  </div>


</form>	
