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
    <div class="span10 form-inline">
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DESCRIPTION'); ?></legend>
        <?php echo $this->form->getLabel('title'); ?> <?php echo $this->form->getInput('title'); ?>
        <hr />
        <fieldset class="adminform ">
          <div class="row-fluid"> 
            <div class="span6">
              <?php echo $this->form->getLabel('accommodation_type'); ?>
              <?php echo $this->form->getInput('accommodation_type'); ?>
            </div>
            <div class="span6">
              <?php echo $this->form->getLabel('property_type'); ?>
              <?php echo $this->form->getInput('property_type'); ?>
            </div>
          </div>


        </fieldset>
        <hr />
        <?php echo $this->form->getLabel('description'); ?>
        <div class="clearfix">

        </div>
        <?php echo $this->form->getInput('description'); ?>
      </fieldset>

      <fieldset> 
        <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_LOCATION_DETAILS'); ?></legend>

        <div class="row-fluid form-inline">

          <div class="span6">
            <div class="control-group">
              <?php echo $this->form->getLabel('department'); ?> 
              <div class="controls">
                <?php echo $this->form->getInput('department'); ?>
              </div>
            </div>
          </div>
          <div class="span6">
            <div class="control-group">
              <?php echo $this->form->getLabel('location_type'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('location_type'); ?>
              </div>
            </div>
          </div>
          <?php echo $this->form->getInput('latitude'); ?>           
          <?php echo $this->form->getInput('longitude'); ?>  
        </div>
        <hr />
        <div class="row-fluid">
          <div class="span8">
            <?php
            // Show the choose location button if this is a parent property or a new property
            if ($this->item->parent_id == 1) :
              ?>
              <div id="map"></div>
            <?php else: ?>
              <p>You cannot edit the location of this property.</p>
              <p>Please navigate to PRN 123456 to update the location of this and all units.</p>
            <?php endif; ?>  
          </div>


          <div class="span4 form-inline">              
            <p><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_EDIT_LOCATION_INSTRUCTIONS') ?></p>
            <div class="control-group">
              <?php echo $this->form->getLabel('city'); ?>
              <div class="controls">
                <?php echo $this->form->getInput('city'); ?>
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
        <div class="row-fluid">
          <div class="span12">        
            <hr />  
            <?php echo $this->form->getLabel('location_details'); ?>
            <?php echo $this->form->getInput('location_details'); ?>
            <hr />
            <?php echo $this->form->getLabel('getting_there'); ?>
            <?php echo $this->form->getInput('getting_there'); ?>
          </div>
        </div>
      </fieldset>    

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
</div>


<?php foreach ($this->form->getFieldset('hidden-details') as $field): ?>

  <?php echo $field->input; ?>
<?php endforeach; ?>
<input type="hidden" name="task" value="helloworld.edit" />
<?php echo JHtml::_('form.token'); ?>
</form>
