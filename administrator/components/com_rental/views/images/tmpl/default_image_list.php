<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
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
<div id="image-message-container" class="alert alert-success hide">
  <h4>Success</h4>
  <span class='icon-publish'></span><?php echo JText::_('COM_IMAGES_ORDERING_SAVED'); ?>
</div>
    <?php if (!empty($this->items)) : ?>
  <ul id="imageList" class="">
    <?php foreach ($this->items as $i => $item): ?>
      <li class="clearfix" id="sort_<?php echo (int) $item->id ?>">  
        <div class="panel panel-default">
          <div class="panel-heading">
            <div class="lead">
              <div class="thumbnail-default pull-left">    
                <?php if ($i == 0) : ?>
                  <span class="icon-default">&nbsp;</span>&nbsp;
                <?php endif; ?> 
                <span class="icon icon-move muted">&nbsp;</span>
              </div>
              <div class="pull-right">
                &nbsp;
                <a class="delete muted" 
                   title="<?php echo Jtext::_('COM_RENTAL_HELLOWORLD_DELETE_IMAGE'); ?>"
                   href="<?php echo '/administrator/index.php?option=com_rental&task=images.delete&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&unit_id=' . (int) $this->items[0]->unit_id ?>">
                  <i class="icon icon-trash"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="panel-body">
            <p>
              <?php if (empty($item->url)) : ?>
                <img class="media-object" src="<?php echo '/images/property/' . (int) $item->unit_id . '/thumb/' . $this->escape($item->image_file_name); ?>" />
              <?php else: ?>
                <img class="media-object" src="<?php echo 'http://' . $item->url ?>" />
              <?php endif; ?>
            </p>
            <p>
              <span class="muted">
                <?php if (empty($item->caption)) : ?>
                  <?php echo JText::_('COM_RENTAL_CAPTION_PLACEHOLDER') ?>
                <?php else: ?>
                  <?php echo JHtml::_('string.truncate', $this->escape($item->caption), 30); ?>
                <?php endif; ?>
              </span>
              <a 
                class="update-caption" 
                href=<?php echo '/administrator/index.php?option=com_rental&tmpl=nohead&view=caption&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&unit_id=' . (int) $this->items[0]->unit_id ?>>
                <i class="icon-pencil-2 icon-white"></i>
                <?php echo empty($item->caption) ? Jtext::_('COM_RENTAL_HELLOWORLD_ADD_CAPTION') : Jtext::_('COM_RENTAL_HELLOWORLD_EDIT_CAPTION'); ?>
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


