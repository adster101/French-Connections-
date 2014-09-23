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


<p>
  <?php echo JText::_('COM_RENTAL_RENTAL_KEY'); ?>
  &nbsp;&nbsp;
  <a class="hasPopover" 
     data-placement="bottom" 
     title="<?php echo JText::_('COM_RENTAL_MANAGE_IMAGES_HELP_TITLE') ?>" 
     data-content="<?php echo JText::_('COM_RENTAL_MANAGE_IMAGES_HELP') ?>">
    <i class="icon icon-info"> </i> 
    <?php echo JText::_('JHELP'); ?>
  </a> 
</p>

<?php if (!empty($this->items)) : ?>
  <ul id="imageList" class="">
    <?php foreach ($this->items as $i => $item): ?>
      <li class="clearfix" id="sort_<?php echo (int) $item->id ?>">  
        <div class="panel panel-default">
          <div class="panel-heading">
            <div class="lead">
              <div class="thumbnail-default pull-left">
                <?php if ($i == 0) : ?>
                  <span class="icon-default pull-left">&nbsp;</span>
                <?php else: ?>
                  <span class="icon pull-left">&nbsp;</span>
                <?php endif; ?>
              </div>
              <span class="icon icon-move muted pull-right">&nbsp;</span>
            </div>
          </div>
          <div class="panel-body">
            <p>
              <img class="media-object" src="<?php echo '/images/property/' . (int) $item->realestate_property_id . '/thumb/' . $this->escape($item->image_file_name); ?>" />
            </p>
            <p class="caption">
              <label for="<?php echo $this->escape($item->image_file_name) ?>">Caption</label>  
              <input placeholder="<?php echo JText::_('COM_RENTAL_CAPTION_PLACEHOLDER') ?>" id="<?php echo $this->escape($item->image_file_name) ?>" class="input span12" type="text" name="jform[caption]" value="<?php echo $this->escape($item->caption); ?>" maxlength="75" />
              <br />
              <span class="muted"><?php echo Jtext::_('COM_RENTAL_HELLOWORLD_REMAINING_CHARS_CAPTION'); ?></span>
              <br /> 
              <span class="message-container"></span>

            </p>
            <p>
              <a class="btn btn-primary update-caption" href="<?php echo '/administrator/index.php?option=com_rental&task=images.updatecaption&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&realestate_property_id=' . (int) $this->items[0]->unit_id ?>" >
                <i class="icon-pencil-2 icon-white"></i>
                <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_UPDATE_CAPTION'); ?>
              </a>  
              <a class="btn btn-danger delete" href="<?php echo '/administrator/index.php?option=com_rental&task=images.delete&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&realestate_property_id=' . (int) $this->items[0]->unit_id ?>">
                <i class="icon icon-trash"></i>
                <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_DELETE_IMAGE'); ?>
              </a>
            </p>
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


