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
      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_TARIFFS_INSTRUCTIONS'); ?>
        <?php foreach ($tariff_field_sets as $fieldset) { ?>
        <fieldset class="adminform">
          <legend>Tariff</legend>
            <ul class="adminformlist tariff-range">
              <?php foreach ($this->form->getFieldset($fieldset->name) as $field) { ?>  
              <li>

                  <?php
                    echo $field->label;
                    echo $field->input;
                  ?>  
              </li>

              <?php } // End of foreach getFieldSet fieldset name ?>
        </fieldset>
      <?php } // End of foreach tariff field sets ?>
    </fieldset>
  </div>
  
  <div class="width-40 fltrt">
    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ADDITIONAL_TARIFFS_DETAIL'); ?></legend>
      <ul class="adminformlist">
        <li><?php echo $this->form->getLabel('id');echo $this->form->getInput('id'); ?></li>
        <li><?php echo $this->form->getLabel('parent_id');echo $this->form->getInput('parent_id'); ?></li>
        <li><?php echo $this->form->getLabel('greeting');echo $this->form->getInput('greeting'); ?></li>
        <li><?php echo $this->form->getLabel('base_currency');echo $this->form->getInput('base_currency'); ?></li>
        <li><?php echo $this->form->getLabel('tariff_based_on');echo $this->form->getInput('tariff_based_on'); ?></li>
        <li><?php echo $this->form->getLabel('linen_costs');echo $this->form->getInput('linen_costs'); ?></li>
      </ul>			
      <?php echo $this->form->getLabel('additional_price_notes'); ?>
      <div class="clr"></div>

      <?php echo $this->form->getInput('additional_price_notes'); ?>

    </fieldset>


    <input type="hidden" name="task" value="tariffs.edit" />
<?php echo JHtml::_('form.token'); ?>
  </div>


</form>	
