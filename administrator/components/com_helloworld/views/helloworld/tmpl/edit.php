<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$params = $this->form->getFieldsets('params');

// Get the user ID
$user = JFactory::getUser();
$userId = $user->get('id');
// And determine the user groups the user is in
$groups = $user->getAuthorisedGroups();
$canChangeOwner = $user->authorise('core.edit.state', 'com_helloworld');
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=helloworld&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
  <div class="row-fluid">
    <div class="span8 ">
      <p>
        <?php
        echo JHTML::_('select.genericlist', $this->languages, 'Language', 'onchange="submitbutton(\'changeLanguage\')"', 'value', 'text', $this->lang);
        echo JText::_('COM_HELLOWORLD_YOU_ARE_EDITING_IN') . '<strong>&nbsp;' . $this->lang . '</strong>';
        ?>
      </p>
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS'); ?></legend>
        <div class="control-group">
          <?php echo $this->form->getLabel('greeting'); ?> <?php echo $this->form->getInput('greeting'); ?>
        </div>
        <?php echo $this->form->getLabel('description'); ?>
        <div class="clearfix"></div>
        <?php echo $this->form->getInput('description'); ?>
      </fieldset>

      <div class="row-fluid">
        <div class="span6">
          <fieldset class="adminform">
            <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS'); ?></legend>
            <?php foreach ($this->form->getFieldset('details') as $field): ?>
              <div class="control-group">
                <?php echo $field->label; ?>
                <div class="controls">
                  <?php echo $field->input; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </fieldset>
        </div>
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
      </div>
    </div>

  <div class="span4">

    <?php echo JHtml::_('sliders.start', 'helloworld-slider'); ?>

    <?php echo JHtml::_('sliders.panel', JText::_('COM_HELLOWORLD_HELLOWORLD_LOCATION_DETAILS'), $name . '-params'); ?>
    <fieldset class="panelform">
      
          <?php
          echo $this->form->getLabel('catid');
          echo $this->form->getInput('catid');
          ?>
        
          <?php
          echo $this->form->getLabel('latitude');
          echo $this->form->getInput('latitude');
          ?>
       
          <?php
          echo $this->form->getLabel('longitude');
          echo $this->form->getInput('longitude');
          ?>
       

        <?php
        // Show the choose location button if this is a parent property or a new property
        if ($this->item->parent_id == 1 || !$this->item->id) :
          ?>

            <p class="">
              <a class="btn btn-primary btn-large modal-button"  rel="{handler: 'ajax', size: {x: 800, y: 600}, onOpen:function(){initialize()}}"
                 href="<?php echo JRoute::_('index.php?option=com_helloworld&view=locate&layout=default&format=raw&id=' . (int) $this->item->id) . JSession::getFormToken() . '=1'; ?>">
                <i class="boot-icon-map-marker boot-icon-white"></i>&nbsp;Please click here to locate property via map</a></p>

        <?php endif; ?>
        
          <?php
          echo $this->form->getLabel('nearest_town');
          echo $this->form->getInput('nearest_town');
          ?>
        
          <?php
          echo $this->form->getLabel('distance_to_coast');
          echo $this->form->getInput('distance_to_coast');
          ?>
     


    </fieldset>
    <?php foreach ($params as $name => $fieldset): ?>
      <?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name . '-params'); ?>
      <?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
        <p class="tip"><?php echo $this->escape(JText::_($fieldset->description)); ?></p>
      <?php endif; ?>
      <fieldset class="panelform" >
        <ul class="adminformlist">
          <?php foreach ($this->form->getFieldset($name) as $field) : ?>
            <li><?php echo $field->label; ?><?php echo $field->input; ?></li>
          <?php endforeach; ?>
        </ul>
      </fieldset>
    <?php endforeach; ?>
    <?php if ($canChangeOwner) { ?>
      <?php echo JHtml::_('sliders.panel', JText::_('COM_HELLOWORLD_HELLOWORLD_OWNER_DETAILS'), $name . '-params'); ?>

      <fieldset class="panelform">
        <ul class="adminformlist">
          <?php foreach ($this->form->getFieldset('owner') as $field): ?>

            <li><?php
        echo $field->label;
        echo $field->input;
            ?></li>

          <?php endforeach; ?>
        </ul>
      </fieldset>
    <?php } ?>
    <?php echo JHtml::_('sliders.end'); ?>
  </div>
  </div>

  <div>
    <input type="hidden" name="task" value="helloworld.edit" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>
