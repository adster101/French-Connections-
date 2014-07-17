<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
?>


<p class="align-right"><?php echo JText::_('COM_RENTAL_RENTAL_KEY'); ?></p>

<?php if (!empty($this->items)) : ?>
  <ul id="imageList" class="list list-striped">
    <?php foreach ($this->items as $i => $item): ?>
      <li class="clearfix" id="sort_<?php echo $item->id ?>">    

        <div class="media">
          <a class="pull-left" data-imageid="<?php echo $item->id ?>">
            <img class="media-object" src="<?php echo '/images/property/' . (int) $item->unit_id . '/thumb/' . $item->image_file_name; ?>" />
          </a>
          <div class="media-body">
            <a class="btn btn-danger pull-right" href="<?php echo '/administrator/index.php?option=com_rental&task=images.delete&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&unit_id=' . (int) $this->items[0]->unit_id ?>">
              <i class="icon icon-trash"></i>
              <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_DELETE_IMAGE'); ?>
            </a> 
            <p class="caption">
              <label>Caption</label>
              <input  class="input input-xlarge " type="text" name="jform[caption]" value="<?php echo $this->escape($item->caption); ?>" maxlength="75" />
              <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_REMAINING_CHARS_CAPTION'); ?>
              <br />
              <a class="btn btn-primary update-caption" href="<?php echo '/administrator/index.php?option=com_rental&task=images.updatecaption&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&unit_id=' . (int) $this->items[0]->unit_id ?>" >
                <i class="icon-pencil-2 icon-white"></i>
                <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_UPDATE_CAPTION'); ?>
              </a>  
              <span class="message-container"></span>
            </p>
            <soan class="thumnail-container">
              <span class="thumbnail-default">        
                <?php if ($i == 0) : ?>
                  <span class="icon-default lead pull-left bottom">&nbsp;</span>
                <?php endif; ?>
              </span>
              </span>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <div class="alert alert-info">
    <?php echo JText::_('COM_RENTAL_RENTAL_IMAGE_GALLERY_EMPTY'); ?>
  </div>
<?php endif; ?>


