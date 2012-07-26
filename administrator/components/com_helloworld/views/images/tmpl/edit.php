<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$image_library_field_sets = $this->form->getFieldSets('library-images');
$image_gallery_field_sets = $this->form->getFieldSets('gallery-images');

// Get the parent ID from the form data
$parent_id = $this->form->getValue('parent_id');

?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&task=images.upload&' . JUtility::getToken() . '=1&id=' . (int) $this->item->id) . '&parent_id=' . $parent_id; ?>" method="post" name="inageUpload" id="tariffs-form">
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
      <ul class="adminformlist clearfix draggable-image-list" id="library">

        <?php foreach ($image_library_field_sets as $fieldset) { ?> 
          <li>
            <?php foreach ($this->form->getFieldset($fieldset->name) as $field) { ?>             
              <?php
              // If this is the image URL then output it, duh.
              if (strpos($field->name, 'url')) {
                ?> 
                <div class="image-container handle">
                  <span class="drag-handle">+-+-+-+-+-+-+-+</span>
                  <img src="<?php echo $field->value; ?>" /> 
                </div>

              <?php }

              if (strpos($field->name, 'caption')) {
                ?>
                <div class="image-control-bar">
                  <?php
                  echo $field->label;
                  echo $field->input;
                  ?>
                </div>
              <?php
              }
              if (strpos($field->name, 'filepath') || strpos($field->name, 'url') || strpos($field->name, 'name')) {
                echo $field->label;
                echo $field->input;
              }
              ?>


            <?php } // End of foreach getFieldSet fieldset name  ?>		

<?php } // End of foreach image field sets    ?>
        </li>
      </ul>	
    </fieldset>

    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_IMAGE_GALLERY'); ?></legend>
      <ul class="adminformlist clearfix draggable-image-list" id="gallery">

          <?php foreach ($image_gallery_field_sets as $fieldset) { ?> 
          <li>
            <?php foreach ($this->form->getFieldset($fieldset->name) as $field) { ?>             
              <?php
              // If this is the image URL then output it, duh.
              if (strpos($field->name, 'url')) {
                ?> 
                <div class="image-container handle">
                  <span class="drag-handle">+-+-+-+-+-+-+-+</span>
                  <img src="<?php echo $field->value; ?>" /> 
                </div>

              <?php }

              if (strpos($field->name, 'caption')) {
                ?>
                <div class="image-control-bar">
                  <?php
                  echo $field->label;
                  echo $field->input;
                  ?>
                </div>
              <?php
              }
              if (strpos($field->name, 'filepath') || strpos($field->name, 'url') || strpos($field->name, 'name')) {
                echo $field->label;
                echo $field->input;
              }
              ?>


  <?php } // End of foreach getFieldSet fieldset name   ?>		

    <?php } // End of foreach image field sets    ?>
        </li>
      </ul>	
    </fieldset>    
    <input type="hidden" name="task" value="images.edit" />
    <?php foreach ($this->form->getFieldset('details') as $field) { ?>
      <?php
      echo $field->input;
      ?>
<?php } ?>    
<?php echo JHtml::_('form.token'); ?>
  </div>
</form>	


