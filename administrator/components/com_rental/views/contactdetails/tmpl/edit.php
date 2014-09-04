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
$data['status'] = $this->status;

// So we can pass them into our layout files
$tabs_layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
$progress_layout = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
?>


<form action="<?php echo JRoute::_('index.php?option=com_rental&view=property&task=edit&property_id=' . (int) $this->item->property_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="" class="span10">
    <?php else : ?>
      <div class="span12">
      <?php endif; ?>
      <?php
      echo $progress_layout->render($data);
      echo $tabs_layout->render($data);
      ?>

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
          <div class="control-group">
            <?php echo $this->form->getLabel('email_2'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('email_2'); ?>
            </div>
          </div>        
        </div>
      </fieldset>            

      <fieldset class="adminform form-horizontal">
        <legend><?php echo JText::_('COM_RENTAL_HELLOWORLD_PERSONAL_WEBSITE'); ?></legend>
        <?php echo $this->form->getLabel('website_note'); ?>

        <div class="control-group">
          <?php echo $this->form->getLabel('website'); ?>
          <div class="controls">
            <?php echo $this->form->getInput('website'); ?>
          </div>
        </div>
      </fieldset>
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_RENTAL_HELLOWORLD_LANGUAGES_SPOKEN'); ?></legend>
        <?php echo $this->form->getControlGroup('languages_spoken'); ?>

        <?php foreach ($this->form->getGroup('languages_spoken') as $field) : ?>
          <?php echo $field->getControlGroup(); ?>
        <?php endforeach; ?>
      </fieldset>
      <fieldset class="adminform form-horizontal">
        <legend><?php echo JText::_('COM_RENTAL_HELLOWORLD_BOOKING_FORM_LEGEND'); ?></legend> 
        <div class="alert alert-notice">
          <?php echo JText::_('COM_RENTAL_HELLOWORLD_BOOKING_FORM_HELP'); ?>
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

    <?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>

      <?php echo $field->input; ?>
    <?php endforeach; ?>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
