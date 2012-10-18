<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');

$params = $this->form->getFieldsets('params');

// Get the user ID
$user = JFactory::getUser();
$userId = $user->get('id');
// And determine the user groups the user is in
$groups = $user->getAuthorisedGroups();
$canChangeOwner = $user->authorise('core.edit.state', 'com_helloworld');
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=helloworld&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate ">
  <div class="row-fluid">
    <div class="span10 form-horizontal">
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DESCRIPTION'); ?></legend>
        <div class="control-group">
          <?php echo $this->form->getLabel('greeting'); ?> <?php echo $this->form->getInput('greeting'); ?>
        </div>
        <fieldset class="adminform">
          <?php foreach ($this->form->getFieldset('details') as $field): ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </fieldset>
        <?php echo $this->form->getLabel('description'); ?>
        <div class="clearfix"></div>
        <?php echo $this->form->getInput('description'); ?>
      </fieldset>
      <div class="row-fluid form-vertical">  
        <div class="span6">
          <fieldset class="adminform">
            <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_OCCUPANCY_DETAILS'); ?></legend>
            <div class="control-group">
              <?php echo $this->form->getLabel('occupancy'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('occupancy'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('single_bedrooms'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('single_bedrooms'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('double_bedrooms'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('double_bedrooms'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('triple_bedrooms'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('triple_bedrooms'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('quad_bedrooms'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('quad_bedrooms'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('twin_bedrooms'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('twin_bedrooms'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('childrens_beds'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('childrens_beds'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('cots'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('cots'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('extra_beds'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('extra_beds'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('bathrooms'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('bathrooms'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('toilets'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('toilets'); ?>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="span6">
          <fieldset>
            <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_LOCATION_DETAILS'); ?></legend>
            <div class="control-group">

              <?php echo $this->form->getLabel('catid'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('catid'); ?>
              </div>
            </div>
            <div class="control-group">

              <?php echo $this->form->getLabel('location_type'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('location_type'); ?>
              </div>
            </div>        
            <div class="control-group">

              <?php echo $this->form->getLabel('latitude'); ?>
              <div class="controls">

                <?php echo $this->form->getInput('latitude'); ?>
              </div>
            </div>
            <div class="control-group">

              <?php echo $this->form->getLabel('longitude'); ?> 
              <div class="controls">

                <?php echo $this->form->getInput('longitude'); ?>
              </div>
            </div>      
            <?php
            // Show the choose location button if this is a parent property or a new property
            if ($this->item->parent_id == 1 || !$this->item->id) :
              ?>

              <p class="">
                <a class="btn btn-primary btn-large modal-button"  rel="{handler: 'ajax', size: {x: 800, y: 600}, onOpen:function(){initialize()}}"
                   href="<?php echo JRoute::_('index.php?option=com_helloworld&view=locate&layout=default&format=raw&id=' . (int) $this->item->id) . JSession::getFormToken() . '=1'; ?>">
                  <i class="icon-map-marker icon-white">&nbsp;</i>Please click here to locate property via map
                </a>
              </p>
            <?php endif; ?>
            <div class="control-group">

              <?php echo $this->form->getLabel('nearest_town'); ?>
              <div class="controls">

                <?php echo $this->form->getInput('nearest_town'); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo $this->form->getLabel('distance_to_coast'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('distance_to_coast'); ?>
              </div>
            </div>
          </fieldset>    
        </div>


      </div>
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
      <?php if ($canChangeOwner) { ?>
        <p>
          <?php
          echo JText::_('COM_HELLOWORLD_YOU_ARE_EDITING_IN') . '<strong>&nbsp;' . $this->lang . '</strong>';
          echo JHTML::_('select.genericlist', $this->languages, 'Language', 'onchange="submitbutton(\'changeLanguage\')"', 'value', 'text', $this->lang);
          ?>
        </p>
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
      <?php } ?>

    </div>
  </div>

  <?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>

    <?php echo $field->input; ?>
  <?php endforeach; ?>
  <input type="hidden" name="task" value="helloworld.edit" />
  <?php echo JHtml::_('form.token'); ?>
</form>
