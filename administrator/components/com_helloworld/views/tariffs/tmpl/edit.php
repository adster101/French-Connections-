<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Get all the fieldsets in the tariffs form group
$tariff_field_sets = $this->form->getFieldSets('tariffs');
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=availability&task=edit&id=' . (int) $this->item->id); ?>" 
      method="post" name="adminForm" id="adminForm" class="form-validate">
  <div class="row-fluid">
    <div class="span8">
    <fieldset class="adminform">		
      <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_TARIFFS'); ?></legend>
      <p>
        <a href="#" class="fltrt">
          <span class="icon-16-info hasTip" 
                title="<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_TARIFFS_HELP'); ?>"></span>
        </a>
      </p>
      <p class="clear"><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_TARIFFS_INSTRUCTIONS'); ?></p>
      <?php foreach ($tariff_field_sets as $fieldset) { ?>
        <fieldset class="adminform">
          <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_TARIFF'); ?></legend>
          <div class="tariff-range row-fluid form-inline">
            <?php foreach ($this->form->getFieldset($fieldset->name) as $field) { ?>  
            <div class="span4">

                <?php
                echo $field->label;
                echo $field->input;
                ?>  
              </div>
            <?php } // End of foreach getFieldSet fieldset name  ?>         </div>

        </fieldset>
      <?php } // End of foreach tariff field sets  ?>
    </fieldset>
  </div>

  <div class="span4">
    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ADDITIONAL_TARIFFS_DETAIL'); ?></legend>
       <?php echo $this->form->getLabel('id');?>
<?php      echo $this->form->getInput('id'); ?>
        <?php echo $this->form->getLabel('parent_id');
      echo $this->form->getInput('parent_id'); ?>
        <?php echo $this->form->getLabel('title');
      echo $this->form->getInput('title'); ?>
        <?php echo $this->form->getLabel('base_currency');
      echo $this->form->getInput('base_currency'); ?>
        <?php echo $this->form->getLabel('tariff_based_on');
      echo $this->form->getInput('tariff_based_on'); ?>
        <?php echo $this->form->getLabel('linen_costs');
      echo $this->form->getInput('linen_costs'); ?>
     	
<?php echo $this->form->getLabel('additional_price_notes'); ?>
     

    <?php echo $this->form->getInput('additional_price_notes'); ?>

    </fieldset>


    <input type="hidden" name="task" value="tariffs.edit" />
<?php echo JHtml::_('form.token'); ?>
  </div>

  </div>
</form>	
