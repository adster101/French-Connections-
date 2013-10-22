<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

// Add various bits of data to an array
$data = array();
$data['property'] = $this->item;
$data['progress'] = $this->progress;

// So we can pass them into our layout files
$tabs_layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
$progress_layout = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
?>


<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=property&task=edit&property_id=' . (int) $this->item->property_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal ">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
      <?php //echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_DETAILS_HELP'); ?>
    </div>
    <div id="" class="span10">
    <?php else : ?>
      <div class="span10 form-inline">
      <?php endif; ?>
      <?php
      echo $progress_layout->render($data);
      echo $tabs_layout->render($data);
      ?>

      <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_CONTACT_DETAILS'); ?></legend> 


      <?php echo $this->form->getLabel('contact_override_note'); ?>

      <fieldset class="adminform">

        <div class="control-group">
          <div class="control-label">
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




      <fieldset class="adminform form-horizontal">

        <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_SMS_CONTACT_DETAILS'); ?></legend> 


        <?php echo $this->form->getLabel('smsprefs'); ?>

        <div class="control-group">
          <?php echo $this->form->getLabel('sms_alert_number'); ?>
          <div class="controls">
            <?php echo $this->form->getInput('sms_alert_number'); ?>
          </div>
        </div>

        <?php if (!$this->item->sms_valid) : ?>
          <div class="control-group">
            <?php echo $this->form->getLabel('dummy_validation_code'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('dummy_validation_code'); ?>
            </div>
          </div>
        <?php elseif ($this->item->sms_valid) : ?>
          <?php echo $this->form->getLabel('sms_valid_message'); ?>
        <?php endif; ?>
      </fieldset>   

      <fieldset class="adminform form-horizontal">
        <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_BOOKING_FORM_LEGEND'); ?></legend> 
        <div class="alert alert-notice">
          <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_BOOKING_FORM_HELP'); ?>
        </div>
        <?php foreach ($this->form->getFieldset('booking-form') as $field): ?>
          <div class="control-group">
            <?php if ($field->name != 'jform[booking_form]') : ?>
              <?php echo $field->label; ?>
            <?php endif; ?>
            <div class="controls">
              <?php echo $field->input; ?>
            </div>
          </div>
        <?php endforeach; ?>

      </fieldset>          
    </div>
  </div>

  <?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>

    <?php echo $field->input; ?>
  <?php endforeach; ?>
  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>
