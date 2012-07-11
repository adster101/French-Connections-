<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');


$image_field_sets = $this->form->getFieldSets('images');

?>


<form action="<?php echo JRoute::_('index.php?option=com_helloworld&task=images.upload&'. JUtility::getToken() .'=1&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="tariffs-form" class="form-validate">
  <div class="width-100">
    <fieldset class="adminform">		
      <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_UPLOAD_IMAGES'); ?></legend>
      <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset('upload') as $field) { ?>
          <li>
            <?php
              //echo $field->label;
              echo $field->input;
            ?>
          </li>
        <?php } ?>
      </ul>			        
    </fieldset>
  </div>
  <?php echo JHtml::_('form.token'); ?>

</form>


<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="tariffs-form" class="form-validate">
  <div class="width-100 fltrt">
    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_IMAGE_GALLERY'); ?></legend>
        <?php foreach ($image_field_sets as $fieldset) { ?>
          
              <?php foreach ($this->form->getFieldset($fieldset->name) as $field) { ?>
                 
                  <?php
                    echo $field->label;
                    echo $field->input;
                  ?>
      
              <?php } // End of foreach getFieldSet fieldset name ?>
		
        <?php } // End of foreach image field sets ?>
      
      <ul class="adminformlist clearfix" id="draggable-image-list">
        <?php foreach ($this->form->getFieldset('image-gallery') as $field) { ?>
          <li>
            <?php
              echo $field->label;
              echo $field->input;
            ?>
          </li>
        <?php } ?>
          
        <li>
          <div class="image-container handle">
            <span class="drag-handle">+-</span>
            <img src="/images/44/Lighthouse.jpg" style="width:100%" />
          </div>  
          <div class="image-control-bar">
            <label id="jform_tariffs_start_date_tariff_1-lbl" for="jform_tariffs_start_date_tariff_1" title="" aria-invalid="false">Start date</label>
            <input type="text" title="Wednesday, 01 August 2012" name="jform[tariffs][start_date][]" id="jform_tariffs_start_date_tariff_1" value="" class="inputbox" aria-invalid="false">
          </div>
        </li>  
        <li>
          <div class="image-container handle">
            <span class="drag-handle">+-</span>
            <img src="/images/44/105713-22.jpg" style="width:100%" /> 
            
          </div> 
          <div class="image-control-bar">
            <p><a href="#">Bar</a></p>
          </div>
        </li>
        <li>
          <div class="image-container handle">
            <span class="drag-handle">+-</span>
            <img src="/images/44/4399-24.jpg" style="width:100%" />
          </div>
          <div class="image-control-bar">
            <p><a href="#">Bar</a></p>
          </div>
        </li>  
       
      
      </ul>	
    </fieldset>
    <input type="hidden" name="task" value="images.edit" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
  
</form>	

