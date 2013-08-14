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
  <div class="row-fluid">
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
              <?php echo $this->form->getLabel('use_invoice_details'); ?>
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('use_invoice_details'); ?>
            </div>
          </div>


          <div id="demo" class="collapse <?php echo ($this->item->use_invoice_details) ? 'out' : 'in' ?> "> 

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
              <?php echo $this->form->getLabel('address'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('address'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('phone_1'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('phone_1'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('phone_2'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('phone_2'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('phone_3'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('phone_3'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('fax'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('fax'); ?>
              </div>
            </div>     
            <div class="control-group">
              <?php echo $this->form->getLabel('email_1'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('email_1'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('email_2'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('email_2'); ?>
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

          <legend><?php echo JText::_(''); ?></legend> 


          <?php echo $this->form->getLabel('booking_form_note'); ?>

          <div class="control-group">
            <div class="control-label">

              <?php echo $this->form->getLabel('booking_form'); ?>
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('booking_form'); ?>
            </div>
          </div>
        </fieldset>          


      </div>
    </div>



    <?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>

      <?php echo $field->input; ?>
    <?php endforeach; ?>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<script>
  jQuery(document).ready(function() {

    jQuery("input[name='jform[use_invoice_details]']").change(function(e) {

      console.log(e);

      var el = e.target;

      var checked = jQuery(el).val();




      if (checked == 1) {
        jQuery('#demo').collapse('hide');

        // Loop over and deactivate all form fields.
        jQuery('#demo').find(':enabled').each(function() {
          jQuery(this).attr('disabled', 'disabled');
        })
      } else if (checked == 0) {

        jQuery('#demo').collapse('show');
        // Loop over and activate all form fields.
        jQuery('#demo').find(':disabled').each(function() {
          jQuery(this).removeAttr('disabled');


        })


      }

    })
  })
</script>