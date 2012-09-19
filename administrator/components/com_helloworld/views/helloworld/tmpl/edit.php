<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$params = $this->form->getFieldsets('params');

// Get the user ID
$user		= JFactory::getUser();
$userId		= $user->get('id');
// And determine the user groups the user is in
$groups = $user->getAuthorisedGroups();
$canChangeOwner = $user->authorise('core.edit.state',	'com_helloworld');

?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=helloworld&task=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="helloworld-form" class="form-validate">
<p>
  <?php
    echo JHTML::_('select.genericlist', $this->languages, 'Language', 'onchange="submitbutton(\'changeLanguage\')"', 'value', 'text', $this->lang);
    echo JText::_('COM_HELLOWORLD_YOU_ARE_EDITING_IN') . '<strong>&nbsp;' . $this->lang . '</strong>';
  ?>
</p>

	<div class="width-60 fltlft">

		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS' ); ?></legend>
			<ul class="adminformlist">
        <?php foreach($this->form->getFieldset('details') as $field): ?>
          <li><?php echo $field->label;echo $field->input;?></li>
        <?php endforeach; ?>
			</ul>
		</fieldset>
    
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_HELLOWORLD_HELLOWORLD_OCCUPANCY_DETAILS' ); ?></legend>
			<ul class="adminformlist">
          <li><?php echo $this->form->getLabel('occupancy');echo $this->form->getInput('occupancy'); ?></li>            
			</ul>
      
      <div class="clr"></div>
      
      <?php echo $this->form->getLabel('bedroomspacer'); ?>
     
      <ul class="adminformlist bedrooms">
        <li>
           <?php echo $this->form->getLabel('single_bedrooms')."<br />";echo $this->form->getInput('single_bedrooms'); ?>
        </li>
        <li>
          <?php echo $this->form->getLabel('double_bedrooms');echo $this->form->getInput('double_bedrooms'); ?>
        </li>
        <li>
          <?php echo $this->form->getLabel('triple_bedrooms');echo $this->form->getInput('triple_bedrooms'); ?>
        </li>
        <li>
          <?php echo $this->form->getLabel('quad_bedrooms');echo $this->form->getInput('quad_bedrooms'); ?>
        </li>
        
        <div class="clr"></div>
        
        <li>
          <?php echo $this->form->getLabel('twin_bedrooms');echo $this->form->getInput('twin_bedrooms'); ?>
        </li>        
        <li>
          <?php echo $this->form->getLabel('childrens_beds');echo $this->form->getInput('childrens_beds'); ?>
        </li>        
        <li>
          <?php echo $this->form->getLabel('cots');echo $this->form->getInput('cots'); ?>
        </li>        
        <li>
          <?php echo $this->form->getLabel('extra_beds');echo $this->form->getInput('extra_beds'); ?>
        </li>
 
        <div class="clr"></div>
    
        <li>
          <?php echo $this->form->getLabel('bathrooms');echo $this->form->getInput('bathrooms'); ?>
        </li>
        <li>
          <?php echo $this->form->getLabel('toilets');echo $this->form->getInput('toilets'); ?>
        </li>
      </ul>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_HELLOWORLD_HELLOWORLD_DESCRIPTION_LEGEND_DETAILS' ); ?></legend>
			<ul class="adminformlist">
        <li>
          <?php 
            echo $this->form->getLabel('greeting');
            echo $this->form->getInput('greeting');
          ?>
        </li>
			</ul>
      <div class="clr"></div>
			<?php echo $this->form->getLabel('description'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('description'); ?>
		</fieldset>
  </div>
 
	<div class="width-40 fltrt">

		<?php echo JHtml::_('sliders.start', 'helloworld-slider'); ?>	

		<?php echo JHtml::_('sliders.panel', JText::_('COM_HELLOWORLD_HELLOWORLD_LOCATION_DETAILS'), $name.'-params');?>
<fieldset class="panelform">
			<ul class="adminformlist">
        <li>
          <?php echo $this->form->getLabel('catid');
          echo $this->form->getInput('catid');?>                    
        </li>
  
        <li>
          <?php echo $this->form->getLabel('latitude');
          echo $this->form->getInput('latitude');?>   
        </li>
        <li class="clearfix">
          <?php echo $this->form->getLabel('longitude');
          echo $this->form->getInput('longitude');?>     
        </li>  
        <?php if ($this->item->parent_id == 1) : ?>
        <li>        
    
           <p class="">
             <a class="btn btn-primary btn-large modal-button"  rel="{handler: 'ajax', size: {x: 800, y: 600}, onOpen:function(){initialize()}}" 
                href="<?php echo JRoute::_('index.php?option=com_helloworld&view=locate&layout=default&format=raw&id=' . (int) $this->item->id) . JUtility::getToken() . '=1'; ?>">
               <i class="boot-icon-map-marker boot-icon-white"></i>&nbsp;Please click here to locate property via map</a></p>
      
        </li>
        <?php endif; ?>
        <li>
          <?php echo $this->form->getLabel('nearest_town');
          echo $this->form->getInput('nearest_town');?>                    
        </li>        
        <li>
          <?php echo $this->form->getLabel('distance_to_coast');
          echo $this->form->getInput('distance_to_coast');?>                    
        </li>  
			</ul>
        
      
		</fieldset>
  <?php foreach ($params as $name => $fieldset): ?>
		<?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
	<?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
		<p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
	<?php endif;?>
		<fieldset class="panelform" >
			<ul class="adminformlist">
	<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
	<?php endforeach; ?>
			</ul>
		</fieldset>
<?php endforeach; ?>
 		<?php if ($canChangeOwner) { ?>
		<?php echo JHtml::_('sliders.panel', JText::_('COM_HELLOWORLD_HELLOWORLD_OWNER_DETAILS'), $name.'-params');?>

<fieldset class="panelform">
		<ul class="adminformlist">
		<?php foreach($this->form->getFieldset('owner') as $field): ?>
		
			<li><?php echo $field->label;echo $field->input;?></li>
			
		<?php endforeach; ?>
				</ul>
	</fieldset>	
<?php } ?>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
 
	<div>
		<input type="hidden" name="task" value="helloworld.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
