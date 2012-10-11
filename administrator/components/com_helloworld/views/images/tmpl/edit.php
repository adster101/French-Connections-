<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="tariffs-form" class="form-validate image-manager">
  <div class="row-fluid">
    <?php foreach($this->item->images->getProperties() as $woot=>$value) : ?>
    <?php if ($this->item->parent_id != 1) { ?>  
      <div class="span6">
      <?php } else { ?>
        <div class="span12"> 
        <?php } ?>
        <fieldset class="adminform">
          <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_IMAGE_'.$woot); ?></legend> 
          <div class="thumbnail">  
            <?php if (!count($this->item->images->$woot->getProperties())): ?>
              <ul class="hero-unit" id="<?php echo $woot; ?>">
                <?php if ($this->item->parent_id != 1) { ?>  
                <li><p class="no-images-in-gallery"><?php echo JText::_('COM_HELLOWORLD_IMAGES_NO_IMAGES_ASSIGNED_TO_GALLERY_'.$woot); ?> </p></li>
                <?php } else { ?>
                <li><p class="no-images-in-gallery"><?php echo JText::_('COM_HELLOWORLD_IMAGES_NO_IMAGES_ASSIGNED_TO_'.$woot); ?> </p></li>
                  
                <?php } ?>
              </ul>
            <?php else: ?>
              <ul class="draggable-image-list" id="<?php echo $woot; ?>">
                <?php foreach ($this->item->images->$woot->getProperties() as $image) { ?>
                  <li>
                    <div class="handle thumbnail">
                      <span class="drag-handle pull-left">
                        <i class="icon-move"> </i>
                      </span>
                      <span class="pull-right">
                        <a rel="woot" 
                           title="<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_IMAGES_DELETE_IMAGE') ?>"
                           href="<?php echo JRoute::_('index.php?option=com_helloworld&view=deleteimage&format=raw&property_id=' . (int) $this->item->id) . '&id=' . (int) $image->id . '&' . JSession::getFormToken() . '=1'; ?>"
                           class="btn btn-danger btn-mini fltrt delete hasTip">
                          <i class="icon-trash icon-white"></i>
                        </a>
                      </span>
                      <span class="pull-right">
                        <a rel="woot" 
                           title="<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_IMAGES_EDIT_CAPTION_IMAGE') ?>"
                           href="<?php echo JRoute::_('index.php?option=com_helloworld&view=caption&format=raw&property_id=' . (int) $this->item->id) . '&id=' . (int) $image->id . '&' . JSession::getFormToken() . '=1'; ?>" 
                           class="btn btn-primary btn-mini fltrt hasTip">
                          <i class="icon-pencil icon-white"></i>
                        </a>
                      </span>
                      <!-- Note that if this is a unit we need to place the image against the parent property ID. -->
                      <?php
                        if ($this->item->parent_id != 1) {
                          $imgPath = JURI::root() . 'images/' . $this->item->parent_id . '/thumb/' . $image->image_file_name;
                        } else {
                          $imgPath = JURI::root() . 'images/' . $this->item->id . '/thumb/' . $image->image_file_name;
                        }

                        $caption = ($image->caption ? $image->caption : JText::_('COM_HELLOWORLD_HELLOWORLD_IMAGES_NO_CAPTION_SET_FOR_THIS_IMAGE'));
                      ?>
                      <p>
                        <img
                          class="hasTip" 
                          title="<?php echo $caption; ?>"
                          src="<?php echo $imgPath ?>" 
                        />
                      </p>
                    </div>
                    <input type="hidden" name="jform[<?php echo $woot?>-images][caption][]" value="<?php echo $image->caption; ?>" />
                    <input type="hidden" name="jform[<?php echo $woot?>-images][image_file_name][]" value="<?php echo $image->image_file_name; ?>" />
                    <input type="hidden" name="jform[<?php echo $woot?>-images][image_file_id][]" value="<?php echo $image->id; ?>" />
                  </li>

                <?php } // End of foreach image field sets   ?>
              <?php endif; ?>
            </ul>
          </div>
        </fieldset>
      </div>
        <?php endforeach; ?>
    </div>
</form>





  <input type="hidden" name="task" value="images.edit" />
  <?php foreach ($this->form->getFieldset('details') as $field) { ?>
    <?php
    echo $field->input;
    ?>
  <?php } ?>    
  <?php echo JHtml::_('form.token'); ?>
</div>

</form>	


