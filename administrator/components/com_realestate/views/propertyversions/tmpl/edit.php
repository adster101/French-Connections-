<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

// $this->status is passed into tabs layout, unwise to couple this way? 
$data = array('status'=>$this->status);
$tabs = new JLayoutFile('realestate_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_realestate/layouts');
var_dump($this->status);
?>
<form action="<?php echo JRoute::_('index.php?option=com_realestate&view=property&task=edit&realestate_property_id=' . (int) $this->item->realestate_property_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
  <?php echo JHtml::_('form.token'); ?>
  <div class="row-fluid">
    <?php if (!empty($this->sidebar)): ?>
      <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        <?php //echo JText::_('COM_REALESTATE_LISTING_DETAILS_HELP'); ?>
      </div>
      <div id="" class="span10">
      <?php else : ?>
        <div class="span12">
        <?php endif; ?>
        <?php
        // Render the progress tabs
        echo $tabs->render($data);
        ?>
        <fieldset class="adminform">       
          <legend>
            <?php echo JText::_('COM_REALESTATE_LOCATION_DETAILS'); ?>
          </legend>  
          <div class="alert alert-notice">
            <?php echo JText::_('COM_REALESTATE_HELLOWORLD_EDIT_LOCATION_INSTRUCTIONS'); ?>   
          </div> 
          <div class="control-group">
            <?php echo $this->form->getLabel('department'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('department'); ?>
            </div>
          </div>
          <div class="control-group">
            <?php echo $this->form->getLabel('city'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('city'); ?>
            </div>
          </div>
          <?php echo $this->form->getInput('map'); ?>
          <?php echo $this->form->getInput('latitude'); ?>
          <?php echo $this->form->getInput('longitude'); ?>
          <div class="control-group">
            <?php echo $this->form->getLabel('airport'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('airport'); ?>
            </div>
          </div>
        </fieldset>
        <fieldset class="adminform">       
          <legend>
            <?php echo JText::_('COM_REALESTATE_DESCRIPTION_LEGEND'); ?>
          </legend>
          <?php foreach ($this->form->getFieldset('description') as $field): ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>       
          <?php endforeach; ?> 
        </fieldset> 
        <fieldset class="adminform">       
          <legend>
            <?php echo JText::_('COM_REALESTATE_PROPERTY_SALE_DETAILS'); ?>
          </legend>
          <?php foreach ($this->form->getFieldset('salesdetails') as $field): ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>       
          <?php endforeach; ?> 
        </fieldset> 
        <fieldset class="adminform">      
          <legend><?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_CONTACT_DETAILS'); ?></legend> 
          <?php echo $this->form->getLabel('contact_override_note'); ?>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('use_invoice_details'); ?>
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('use_invoice_details'); ?>
            </div>
          </div>
          <div id="contactDetails"> 
            <div class="control-group">
              <?php echo $this->form->getLabel('first_name'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('first_name'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('surname'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('surname'); ?>
              </div>
            </div>

            <div class="control-group">            

              <?php echo $this->form->getLabel('phone_1'); ?>
              <div class="controls"> 
                <?php echo $this->form->getInput('phone_1'); ?> 
                <?php echo $this->form->getLabel('phone_1_note'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('email_1'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('email_1'); ?>
              </div>
            </div>              
          </div>
        </fieldset>            
      </div>
    </div>
    <?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>
      <?php echo $field->input; ?>
    <?php endforeach; ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="next" value="<?php echo base64_encode(JRoute::_('index.php?option=com_realestate&task=images.manage&realestate_property_id=' . (int) $this->item->realestate_property_id, false)); ?>" />
</form>

