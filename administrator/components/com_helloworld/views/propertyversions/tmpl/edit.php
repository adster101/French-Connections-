<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$data = array();
$data['property'] = $this->item;
$data['progress'] = $this->progress;
?>

<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=property&task=edit&parent_id=' . (int) $this->item->parent_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate ">
  <div class="row-fluid">
    <?php if (!empty($this->sidebar)): ?>
      <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        <?php //echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_DETAILS_HELP'); ?>
      </div>
      <div id="" class="span8">
      <?php else : ?>
        <div class="span10 form-inline">
        <?php endif; ?>
        <?php
          $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
          echo $layout->render($data);
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
            <div class="span8">
              <?php echo $this->form->getInput('map'); ?>
              <?php echo $this->form->getInput('latitude'); ?>
              <?php echo $this->form->getInput('longitude'); ?>
            </div>
            <div class="span4 form-inline">
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_EDIT_LOCATION_INSTRUCTIONS'); ?>
              <hr />
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


      <div class="span2 form-vertical">
        <fieldset class="adminform">
          <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ADDITIONAL_DETAILS'); ?></legend>
          <?php foreach ($this->form->getFieldset('additional-details') as $field): ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </fieldset>

        <fieldset class="panelform">
          <?php foreach ($this->form->getFieldset('owner') as $field): ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>

          <?php endforeach; ?>
        </fieldset>
        <fieldset class="panelform">
          <?php foreach ($this->form->getFieldset('notes') as $field): ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>

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