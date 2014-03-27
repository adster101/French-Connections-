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
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=helloworld&layout=edit&unit_id=' . (int) $this->item->unit_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
  <div class="row-fluid">
    <?php if (!empty($this->sidebar)): ?>
      <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
      </div>
      <div id="" class="span8">
      <?php else : ?>
        <div class="span10">
        <?php endif; ?>
        <?php
        $progress = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
        echo $progress->render($data);

        $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
        echo $layout->render($data);
        ?>

        <fieldset class="adminform form-inline">
          <legend><?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DESCRIPTION', $this->item->unit_title); ?></legend>
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

        <fieldset class="adminform form-vertical">
          <legend><?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_OCCUPANCY_DETAILS', $this->item->unit_title); ?></legend>
          <div class="row-fluid">
            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('occupancy'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('occupancy'); ?>
                </div>
              </div>
            </div>
            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('single_bedrooms'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('single_bedrooms'); ?>
                </div>
              </div>
            </div>
            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('double_bedrooms'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('double_bedrooms'); ?>
                </div>
              </div>
            </div>
            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('triple_bedrooms'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('triple_bedrooms'); ?>
                </div>
              </div>
            </div>
            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('quad_bedrooms'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('quad_bedrooms'); ?>
                </div>
              </div>
            </div>
            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('twin_bedrooms'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('twin_bedrooms'); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="row-fluid">

            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('childrens_beds'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('childrens_beds'); ?>
                </div>
              </div>
            </div>
            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('cots'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('cots'); ?>
                </div>
              </div>
            </div>
            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('extra_beds'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('extra_beds'); ?>
                </div>
              </div>
            </div>
            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('bathrooms'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('bathrooms'); ?>
                </div>
              </div>
            </div>
            <div class="span2">
              <div class="control-group">
                <?php echo $this->form->getLabel('toilets'); ?>
                <div class="controls">
                  <?php echo $this->form->getInput('toilets'); ?>
                </div>
              </div>
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend><?php echo JText::sprintf('COM_HELLOWORLD_FACILITIES_LEGEND', $this->item->unit_title); ?></legend>
          <div class="alert alert-notice">
            <span class="icon icon-flag">&nbsp</span><?php echo JText::_('COM_HELLOWORLD_FACILITIES_BLURB'); ?>
          </div>
        </fieldset>
        <h4><?php echo JText::_('COM_HELLOWORLD_ACCOMMODATION_INTERNAL_FACILITIES'); ?></h4>
        <fieldset class="panelform">
          <?php foreach ($this->form->getFieldset('internal_facilities') as $field) : ?>
            <div class="row-fluid">
              <div class="span12">
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </fieldset>
        <h4><?php echo JText::_('COM_HELLOWORLD_ACCOMMODATION_EXTERNAL_FACILITIES'); ?></h4>
        <fieldset class="panelform">
          <?php foreach ($this->form->getFieldset('external_facilities') as $field) : ?>
            <p><?php echo $field->label; ?>
              <?php echo $field->input; ?></p>
          <?php endforeach; ?>
        </fieldset>
        <h4><?php echo JText::_('COM_HELLOWORLD_ACCOMMODATION_KITCHEN_FACILITIES'); ?></h4>
        <fieldset class="panelform">
          <?php foreach ($this->form->getFieldset('kitchen_facilities') as $field) : ?>
            <p><?php echo $field->label; ?></p>
            <?php echo $field->input; ?>
          <?php endforeach; ?>
        </fieldset>
        <h4><?php echo JText::_('COM_HELLOWORLD_ACCOMMODATION_SUITABILITY_FACILITIES'); ?></h4>
        <fieldset class="panelform">
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
  <?php echo JHtml::_('form.token'); ?>
</form>
