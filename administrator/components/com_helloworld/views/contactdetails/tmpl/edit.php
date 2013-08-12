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
        <fieldset class="adminform">

          <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_CONTACT_DETAILS'); ?></legend> 


          <?php echo $this->form->getLabel('contact_override_note'); ?>

          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('override_invoice_details'); ?>
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('override_invoice_details'); ?>
            </div>
          </div>
        </fieldset>            


        <div id="demo" class="collapse"> 

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
            <?php echo $this->form->getLabel('phone_2'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('phone_2'); ?>
            </div>
          </div>
        </div>






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
          <hr />
        </fieldset>          


      </div>
    </div>



    <?php //foreach ($this->form->getFieldset('hidden-details') as $field):  ?>

    <?php //echo $field->input;  ?>
    <?php //endforeach; ?>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<script>
  jQuery(document).ready(function() {

    jQuery("input[name='jform[override_invoice_details]']").change(function(e) {

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