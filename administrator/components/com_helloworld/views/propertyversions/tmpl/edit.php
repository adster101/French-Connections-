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

$fieldsets = $this->form->getFieldSets('citiestowns');
$amenities = $this->form->getGroup('amenities');
?>

<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=property&task=edit&property_id=' . (int) $this->item->property_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
  <div class="row-fluid">
    <?php if (!empty($this->sidebar)): ?>
      <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        <?php //echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_DETAILS_HELP'); ?>
      </div>
      <div id="" class="span10">
      <?php else : ?>
        <div class="span12 form-inline">
        <?php endif; ?>
        <?php
        echo $progress_layout->render($data);
        echo $tabs_layout->render($data);
        ?>
        <fieldset class="adminform form-horizontal">
          <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_DETAILS'); ?></legend>
          <div class="control-group">
            <?php echo $this->form->getLabel('title'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('title'); ?>
            </div>
          </div>
          <hr />
        </fieldset>

        <fieldset>
          <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_LOCATION_DETAILS'); ?></legend>
          <div class="row-fluid">
            <div class="span5"> 
              <div class="alert alert-notice">
                <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_EDIT_LOCATION_INSTRUCTIONS'); ?>             
              </div>
              <?php echo $this->form->getInput('map'); ?>
              <?php echo $this->form->getInput('latitude'); ?>
              <?php echo $this->form->getInput('longitude'); ?>
            </div>
            <div class="span6 offset1">
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
              <div class="control-group">
                <?php echo $this->form->getLabel('location_type'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('location_type'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('distance_to_coast'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('distance_to_coast'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('1', 'amenities'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('1', 'amenities'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('2', 'amenities'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('2', 'amenities'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('bakery', 'amenities'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('bakery', 'amenities'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('bakery', 'amenities'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('bakery', 'amenities'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('bakery', 'amenities'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('bakery', 'amenities'); ?>
                </div>
              </div>
              <div class="control-group">
                <?php echo $this->form->getLabel('bakery', 'amenities'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('bakery', 'amenities'); ?>
                </div>
              </div>
            </div>
          </div>

          <hr />
          <div class="row-fluid">
            <div class="span6">
              <?php echo $this->form->getLabel('location_details'); ?>
              <?php echo $this->form->getInput('location_details'); ?>
            </div>
            <div class="span6">
              <?php echo $this->form->getLabel('getting_there'); ?>
              <?php echo $this->form->getInput('getting_there'); ?>
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
