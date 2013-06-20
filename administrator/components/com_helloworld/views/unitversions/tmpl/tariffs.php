<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Get all the fieldsets in the tariffs form group
$tariff_field_sets = $this->form->getFieldSet('tariffs');
$data = array('item' => $this->item, 'progress' => $this->progress);
$counter = 0;
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=unitversions&layout=tariffs&unit_id=' . (int) $this->item->unit_id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">
  <div class="row-fluid">
    <?php if (!empty($this->sidebar)): ?>
      <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
      </div>
      <div id="" class="span8">
      <?php else : ?>
        <div class="span10">
        <?php endif; ?>
        <!-- Listing status and tab layouts start -->
        <?php
        $progress = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
        echo $progress->render($data);

        $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
        echo $layout->render($data);
        ?>

        <!-- Listing status and tab layouts end -->

        <div class="row-fluid">
          <div class="span9">
            <fieldset class="adminform">
              <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_TARIFFS'); ?></legend>

              <p class="clear"><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_TARIFFS_INSTRUCTIONS'); ?></p>

              <div class="tariff-range row-fluid">
                <?php foreach ($this->form->getFieldset('tariffs') as $field) : ?>
                  <?php if ($counter % 3 === 0) : // Output a new row every third ?> 
                    <div class="row-fluid">
                    <?php endif; ?>
                    <div class="span4">
                      <?php
                      echo $field->label;
                      echo $field->input;
                      ?>               
                    </div>      
                    <?php if (($counter % 3 === 2)) : ?>
                      </div>
                      <hr />
                      <div class="row-fluid">
                    <?php endif; ?>
                    <?php $counter++; ?>
                  <?php endforeach; // End of foreach getFieldSet fieldset name  ?> 
                </div>
            </fieldset>
          </div>
          <div class="span3">
            <fieldset class="adminform">
              <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ADDITIONAL_TARIFFS_DETAIL'); ?></legend>
              <?php
              echo $this->form->getLabel('id');
              echo $this->form->getInput('id');

              echo $this->form->getLabel('changeover_day');
              echo $this->form->getInput('changeover_day');

              echo $this->form->getLabel('base_currency');
              echo $this->form->getInput('base_currency');

              echo $this->form->getLabel('note21');

              echo $this->form->getLabel('tariff_based_on');
              echo $this->form->getInput('tariff_based_on');

              echo $this->form->getLabel('linen_costs');
              echo $this->form->getInput('linen_costs');

              echo $this->form->getLabel('additional_price_notes');
              echo $this->form->getInput('additional_price_notes');
              ?>
            </fieldset>
          </div>
        </div>
      </div>

      <div class="span2">
        <h4>Extra help and what not</h4>
      </div>
    </div>
  </div>
  <?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>
    <?php echo $field->input; ?>
  <?php endforeach; ?>
  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>
