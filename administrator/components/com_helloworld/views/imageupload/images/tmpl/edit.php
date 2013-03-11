<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$format = JRequest::getVar('format', 'html', 'GET', 'string');
$image_properties = $this->item->images->getProperties();
$library_images = array();
$gallery_images = array();

if (array_key_exists('library', $image_properties)) {
  $library_images = $image_properties['library']->getProperties();
}

if (array_key_exists('gallery', $image_properties)) {
  $gallery_images = $image_properties['gallery']->getProperties();
}
?>
<?php if ($format == 'html') : ?>
  <div id="collapseUpload" class="collapse row-fluid">
    <div class="span12">
      <form action="<?php echo JRoute::_('index.php?option=com_helloworld&task=images.upload&' . JSession::getFormToken() . '=1&id=' . (int) $this->item->id) . '&parent_id=' . $this->item->parent_id; ?>" method="post" name="imageUpload" id="imageForm">
        <div class="row-fluid">
          <div class="span12">
            <h2><?php echo JText::_('COM_HELLOWORLD_IMAGES_UPLOAD_IMAGES'); ?></h2>
            <p><?php echo JText::_('COM_HELLOWORLD_IMAGES_UPLOAD_IMAGES_HELP'); ?></p>

            <fieldset class="adminform">		
              <div id="image-queue">
                <?php foreach ($this->form->getFieldset('upload') as $field) { ?>
                  <?php
                  echo $field->input;
                  ?>
                <?php } ?>
              </div>       
            </fieldset>
            <div class="clearfix">
              <button class="btn btn-large" id="explore">
                <i class="icon-search"></i> 
                <?php echo JText::_('COM_HELLOWORLD_IMAGES_BROWSE_IMAGES'); ?>
              </button>      
              <button class="btn btn-primary btn-large">
                <i class="icon-upload icon-white"></i> 
                <?php echo JText::_('COM_HELLOWORLD_IMAGES_UPLOAD_IMAGES'); ?>
              </button>
            </div>
            <?php echo JHtml::_('form.token'); ?>
            <?php foreach ($this->form->getFieldset('details') as $field) { ?>
              <?php
              echo $field->label;
              echo $field->input;
              ?>   
            <?php } ?>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>


<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate image-manager">
  <div class="row-fluid">
    <?php foreach ($image_properties as $woot => $value) : ?>
      <?php if (array_key_exists('gallery', $image_properties) && array_key_exists('library', $image_properties)) { ?>  
        <div class="span6">
        <?php } else { ?>
          <div class="span12"> 
          <?php } ?>           
          <div class="thumbnail">  
            <fieldset class="adminform">
              <legend><?php echo JText::_('COM_HELLOWORLD_IMAGES_IMAGE_' . $woot); ?></legend> 
              <?php if (!count($image_properties[$woot])): ?>
                <ul class="hero-unit" id="<?php echo $woot; ?>">
                  <?php if ($this->item->parent_id != 1) { ?>  
                    <li><p class="no-images-in-gallery"><?php echo JText::_('COM_HELLOWORLD_IMAGES_NO_IMAGES_ASSIGNED_TO_GALLERY_' . $woot); ?> </p></li>
                  <?php } else { ?>
                    <li><p class="no-images-in-gallery"><?php echo JText::_('COM_HELLOWORLD_IMAGES_NO_IMAGES_ASSIGNED_TO_' . $woot); ?> </p></li>

                  <?php } ?>
                </ul>
              <?php else: ?>
                <ul class="draggable-image-list" id="<?php echo $woot; ?>">
                  <?php foreach ($this->item->images->$woot->getProperties() as $image) : ?>

                    <?php
                    if (array_key_exists($image->image_file_name, $gallery_images) && $woot == 'library') {
                      $show = false;
                    } else {
                      $show = true;
                    }
                    ?>

                    <?php if ($show) : ?>
                      <li>
                        <div class="handle thumbnail">
                          <span class="drag-handle pull-left">
                            <i class="icon-move"> </i>
                          </span>
                          <span class="pull-right">
                            <a rel="woot" 
                               title="<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_IMAGES_DELETE_IMAGE') ?>"
                               href="<?php echo JRoute::_('index.php?option=com_helloworld&view=deleteimage&format=raw&parent_id=' . (int) $this->item->parent_id . '&property_id=' . (int) $this->item->id) . '&id=' . (int) $image->id . '&' . JSession::getFormToken() . '=1'; ?>"
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
                            $imgPath = JURI::root() . 'images/' . $this->item->parent_id . '/thumbs/' . str_replace('.', '_175x100.', $image->image_file_name);
                          } else {
                            $imgPath = JURI::root() . 'images/' . $this->item->id . '/thumbs/' . str_replace('.', '_175x100.', $image->image_file_name);
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
                        <input type="hidden" name="jform[<?php echo $woot ?>-images][caption][]" value="<?php echo $image->caption; ?>" />
                        <input type="hidden" name="jform[<?php echo $woot ?>-images][image_file_name][]" value="<?php echo $image->image_file_name; ?>" />
                        <input type="hidden" name="jform[<?php echo $woot ?>-images][image_file_id][]" value="<?php echo $image->id; ?>" />
                      </li>
                    <?php endif; ?>
                  <?php endforeach; // End of foreach image field sets      ?>
                <?php endif; ?>
              </ul>
            </fieldset>  
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <input type="hidden" name="task" value="images.edit" />
    <?php echo JHtml::_('form.token'); ?>
  </div>

</form>	


