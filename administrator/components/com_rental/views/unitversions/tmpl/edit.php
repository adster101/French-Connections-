<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');
// Get the user ID
$user = JFactory::getUser();
$userId = $user->get('id');
// And determine the user groups the user is in

$data = array('item' => $this->item, 'progress' => $this->progress);
?>

<form action="<?php echo JRoute::_('index.php?option=com_rental&view=helloworld&layout=edit&unit_id=' . (int) $this->item->unit_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
  <div class="row-fluid">
    <?php if (!empty($this->sidebar)): ?>
      <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
      </div>
      <div id="" class="span10">
      <?php else : ?>
        <div class="span12">
        <?php endif; ?>
        <?php
        $progress = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
        echo $progress->render($data);

        $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
        echo $layout->render($data);
        ?>

        <fieldset class="adminform form-inline">
          <legend><?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_ACCOMMODATION_DESCRIPTION', $this->item->unit_title); ?></legend>
          <div class="control-group">
            <?php echo $this->form->getLabel('unit_title'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('unit_title'); ?>
            </div>
          </div>
          <div class="control-group">
            <?php echo $this->form->getLabel('accommodation_type'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('accommodation_type'); ?>
            </div>
          </div>
          <div class="control-group">
            <?php echo $this->form->getLabel('property_type'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('property_type'); ?>
            </div>
          </div>
          <hr />
          <div class="control-group">
            <?php echo $this->form->getLabel('description'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('description'); ?>
            </div>
        </fieldset>

        <fieldset class="adminform ">
          <legend><?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_OCCUPANCY_DETAILS', $this->item->unit_title); ?></legend>
          <?php foreach ($this->form->getFieldset('occupancy') as $field) : ?>
            <div class="bedrooms-container">
              <div class="control-group">
                <?php echo $field->label; ?>
                <div class="controls">
                  <?php echo $field->input; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </fieldset>
        <fieldset class="adminform ">
          <legend><?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_BATHROOM_DETAILS', $this->item->unit_title); ?></legend>
          <?php foreach ($this->form->getFieldset('douches') as $field) : ?>
            <div class="bedrooms-container">
              <div class="control-group">
                <?php echo $field->label; ?>
                <div class="controls">
                  <?php echo $field->input; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </fieldset>
        <fieldset>
          <legend><?php echo JText::sprintf('COM_RENTAL_FACILITIES_LEGEND', $this->item->unit_title); ?></legend>
          <div class="alert alert-notice">
            <span class="icon icon-flag"></span>&nbsp<?php echo JText::_('COM_RENTAL_FACILITIES_BLURB'); ?>
          </div>
        </fieldset>
        <fieldset class="panelform">  
          <legend><?php echo JText::_('COM_RENTAL_ACCOMMODATION_INTERNAL_FACILITIES'); ?></legend>
          <?php foreach ($this->form->getFieldset('internal_facilities') as $field) : ?>
            <?php echo $field->label; ?>
            <?php echo $field->input; ?>
          <?php endforeach; ?>
        </fieldset>
        <hr />
        <fieldset class="panelform">
          <legend><?php echo JText::_('COM_RENTAL_ACCOMMODATION_EXTERNAL_FACILITIES'); ?></legend>
          <?php foreach ($this->form->getFieldset('external_facilities') as $field) : ?>
            <p><?php echo $field->label; ?>
              <?php echo $field->input; ?></p>
          <?php endforeach; ?>
        </fieldset>
        <hr />
        <fieldset class="panelform">
          <legend><?php echo JText::_('COM_RENTAL_ACCOMMODATION_KITCHEN_FACILITIES'); ?></legend>
          <?php foreach ($this->form->getFieldset('kitchen_facilities') as $field) : ?>
            <p><?php echo $field->label; ?></p>
            <?php echo $field->input; ?>
          <?php endforeach; ?>
        </fieldset>
        <hr />
        <fieldset class="panelform">
          <legend><?php echo JText::_('COM_RENTAL_ACCOMMODATION_SUITABILITY_FACILITIES'); ?></legend>
          <?php foreach ($this->form->getFieldset('suitability') as $field) : ?>
            <p><?php echo $field->label; ?></p>
            <?php echo $field->input; ?>
          <?php endforeach; ?>
        </fieldset>
      </div>
    </div>
  </div>
  <?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>
    <?php echo $field->input; ?>
  <?php endforeach; ?>
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="next" value="<?php echo base64_encode(JRoute::_('index.php?option=com_rental&task=images.manage&unit_id=' . (int) $this->item->unit_id . '&' . JSession::getFormToken() . '=1', false)); ?>" />
  <?php echo JHtml::_('form.token'); ?>
</form>
