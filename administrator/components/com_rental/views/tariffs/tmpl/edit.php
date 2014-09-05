<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Get all the fieldsets in the tariffs form group
$tariff_field_sets = $this->form->getFieldSet('tariffs');
$data = array('item' => $this->item, 'progress' => $this->progress, 'status' => $this->status);
$counter = 0;
?>

<form action="<?php echo JRoute::_('index.php?option=com_rental&view=tariffs&layout=edit&unit_id=' . (int) $this->item->unit_id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
  <div class="row-fluid">
    <?php if (!empty($this->sidebar)): ?>
      <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
      </div>
      <div id="" class="span10">
      <?php else : ?>
        <div class="span12">
        <?php endif; ?>
        <!-- Listing status and tab layouts start -->
        <?php
        $progress = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
        echo $progress->render($data);

        $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
        echo $layout->render($data);
        ?>
        <div class="alert alert-notice">
          <p><?php echo JText::_('COM_RENTAL_HELLOWORLD_TARIFFS_INSTRUCTIONS'); ?></p>
        </div>
        <div class="row-fluid">
          <div class="span8">
            <fieldset class="adminform">
              <legend><?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_TARIFFS', $this->item->unit_title); ?></legend>
              <div class="tariff-range">
                <ol class="list-striped">
                <?php foreach ($this->form->getFieldset('unit-tariffs') as $field) : ?>                
                  <?php if ($counter % 3 === 0) : // Output a new row every third  ?> 
                  <li class="clearfix">
                    <?php endif; ?>     
                    <div class="tariffs-container">
                      <?php
                      echo $field->label;
                      echo $field->input;
                      ?>            
                    </div>
                    <?php if (($counter % 3 === 2)) : ?>
                      
                  </li>
                  <?php endif; ?>
                  <?php $counter++; ?>
                <?php endforeach; // End of foreach getFieldSet fieldset name   ?> 
                </ul>
              </div>
            </fieldset>
            <fieldset>
               <div class="control-group">
                <?php echo $this->form->getLabel('copy_tariffs'); ?> 
                <div class="controls">
                  <?php echo $this->form->getInput('copy_tariffs'); ?>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="span4">
            <!-- Listing status and tab layouts end -->
            <fieldset class="adminform form-vertical">
              <legend><?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_ADDITIONAL_TARIFFS_DETAIL', $this->item->unit_title); ?></legend>

              <div class="control-group ">
                <?php echo $this->form->getLabel('changeover_day'); ?> 
                <div class="controls">
                  <?php echo $this->form->getInput('changeover_day'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('base_currency'); ?> 
                <div class="controls">
                  <?php echo $this->form->getInput('base_currency'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('tariff_based_on'); ?> 
                <div class="controls">
                  <?php echo $this->form->getInput('tariff_based_on'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('linen_costs'); ?> 
                <div class="controls">
                  <?php echo $this->form->getInput('linen_costs'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('additional_price_notes'); ?> 
                <div class="controls">
                  <?php echo $this->form->getInput('additional_price_notes'); ?>
                </div>
              </div>
            </fieldset>
          </div>
        </div>     
      </div>
    </div>
  </div>
  <?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>
    <?php echo $field->input; ?>
  <?php endforeach; ?>
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="next" value="<?php echo base64_encode(JRoute::_('index.php?option=com_rental&task=availability.edit&unit_id=' . (int) $this->item->unit_id . '&' . JSession::getFormToken() . '=1', false)); ?>" />

  <?php echo JHtml::_('form.token'); ?>
</form>
