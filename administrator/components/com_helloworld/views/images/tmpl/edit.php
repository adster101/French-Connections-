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

<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="tariffs-form" class="form-validate">
  <?php 
    // TO DO: Break this out into a separate template so it is easier to maintain
    if (count($image_library_field_sets)) { ?>
    <div class="width-50 fltlft">
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_IMAGE_LIBRARY'); ?></legend>
        <ul class="adminformlist clearfix draggable-image-list" id="library">
    <?php foreach ($image_library_field_sets as $fieldset) { ?> 
            <li>
    <?php foreach ($this->form->getFieldset($fieldset->name) as $field) { ?>  
       
      <?php if ($field->fieldname == 'image_file_name') { ?> 
                  <div class="image-container handle">
                    <span class="drag-handle clearfix bottom"><i class="boot-icon-move"></i></span>
                    <!-- Note that if this is a unit we need to place the image against the parent property ID. -->
                  <?php if ($parent_id != 1) { ?>
                      <img src="<?php echo JURI::root() . 'images/' . $parent_id . '/thumb/' . $field->value; ?>" />
                  <?php } else { ?>
                      <img src="<?php echo JURI::root() . 'images/' . $this->item->id . '/thumb/' . $field->value; ?>" />
        <?php } ?>
                  </div>
                  <?php }
                  if ($field->fieldname == 'caption') {
                    ?>
                  <div class="image-control-bar clearfix">          
                    <div class="width-100">          
                    <?php
                    echo $field->label;
                    echo $field->input;
                    ?>
                    </div>
                  </div>
                    <?php
                    }
                    if ($field->fieldname == 'image_file_id') {
                      echo $field->label;
                      echo $field->input;
                      ?>
                  <div class="width-20 fltrt">
                    <a rel="woot" href="<?php echo JRoute::_('index.php?option=com_helloworld&view=deleteimage&format=raw&property_id=' . (int) $this->item->id) . '&id=' . (int) $field->value . '&' . JUtility::getToken() . '=1'; ?>"
                       class="btn btn-danger btn-mini fltrt delete">
                      <i class="boot-icon-trash boot-icon-white"></i>
                    </a>
                  </div>
                  <div class="width-20 fltrt">
                    <a rel="woot" href="<?php echo JRoute::_('index.php?option=com_helloworld&view=caption&format=raw&property_id=' . (int) $this->item->id) . '&id=' . (int) $field->value . '&' . JUtility::getToken() . '=1'; ?>" 
                       class="btn btn-primary btn-mini fltrt edit" title="<?php echo JText::_('COM_HELLOWORLD_IMAGES_EDIT_CAPTION') ?>">
                      <i class="boot-icon-edit boot-icon-white"></i>
                    </a>
                  </div>
      <?php
      }

      if (strpos($field->name, 'image_file_name')) {
        echo $field->label;
        echo $field->input;
      }
      ?>
              <?php } // End of foreach getFieldSet fieldset name  ?>		
            <?php } // End of foreach image field sets    ?>
          </li>
        </ul>	
      </fieldset>
    </div>
<?php } ?>
<?php if (count($image_library_field_sets)) { ?>
    <div class="width-50 fltrt">
  <?php } else { ?>
      <div class="width-100 fltrt"> 
    <?php } ?>
      <fieldset class="adminform">
        <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_IMAGE_GALLERY'); ?></legend>
        <ul class="adminformlist clearfix draggable-image-list" id="gallery">
<?php foreach ($image_gallery_field_sets as $fieldset) { ?>
            <li>
            <?php foreach ($this->form->getFieldset($fieldset->name) as $field) { ?>             
              <?php if ($field->fieldname == 'image_file_name') { ?> 
                  <div class="image-container handle">
                   
                    <span class="drag-handle clearfix bottom"><i class="boot-icon-move"></i></span>
                    <!-- Note that if this is a unit we need to place the image against the parent property ID. -->
                  <?php if ($parent_id != 1) { ?>
                      <img src="<?php echo JURI::root() . 'images/' . $parent_id . '/thumb/' . $field->value; ?>" />
      <?php } else { ?>
                      <img src="<?php echo JURI::root() . 'images/' . $this->item->id . '/thumb/' . $field->value; ?>" />
                    <?php } ?></span>
                  </div>
                  <?php }
                  if ($field->fieldname == 'caption') {
                    ?>
                  <div class="image-control-bar clearfix">          
                    <div class="width-100">          
                  <?php
                  echo $field->label;
                  echo $field->input;
                  ?>
                    </div>
                  </div>
                    <?php
                    }
                    if ($field->fieldname == 'image_file_id') {
                      echo $field->label;
                      echo $field->input;
                      ?>
                  <div class="width-20 fltrt">
                    <a rel="woot" href="<?php echo JRoute::_('index.php?option=com_helloworld&view=deleteimage&format=raw&property_id=' . (int) $this->item->id) . '&id=' . (int) $field->value . '&' . JUtility::getToken() . '=1'; ?>"
                       class="btn btn-danger btn-mini fltrt delete">
                      <i class="boot-icon-trash boot-icon-white"></i>
                    </a>
                  </div>
                  <div class="width-20 fltrt">
                    <a rel="woot" href="<?php echo JRoute::_('index.php?option=com_helloworld&view=caption&format=raw&property_id=' . (int) $this->item->id) . '&id=' . (int) $field->value . '&' . JUtility::getToken() . '=1'; ?>" 
                       class="btn btn-primary btn-mini fltrt edit">
                      <i class="boot-icon-edit boot-icon-white"></i>
                    </a>
                  </div>
                <?php
                }
                if (strpos($field->name, 'image_file_name')) {
                  echo $field->label;
                  echo $field->input;
                }
                ?>
              <?php } // End of foreach getFieldSet fieldset name  ?>		
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


