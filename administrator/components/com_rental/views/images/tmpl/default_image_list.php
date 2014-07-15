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
<style>

  #imageList li {
    position:relative;
    padding-left: 35px;
  }

  #imageList li:hover {
    cursor:move;
  }

  #imageList li:before {
    font-family: icomoon;
    content: "\7a";
    font-size:21px;
    position:absolute;
    top:50px;
    left:0;
    padding:9px;
  }

  .ui-state-highlight {
    height:120px;
    border:dotted 1px #333;
  }
</style>

<?php foreach ($this->items as $i => $item): ?>
  <li class="clearfix" id="sort_<?php echo $item->id ?>">
    <div class="media">
      <a class="pull-left" data-imageid="<?php echo $item->id ?>">
        <img class="media-object" src="<?php echo '/images/property/' . (int) $item->unit_id . '/thumb/' . $item->image_file_name; ?>" />
      </a>
      <div class="media-body">
        <p>
          <a class="btn btn-primary" href="<?php echo '/administrator/index.php?option=com_rental&task=images.updatecaption&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&unit_id=' . (int) $this->items[0]->unit_id ?>" >
            <i class="icon-pencil-2 icon-white"></i>
            <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_UPDATE_CAPTION'); ?>
          </a>  

          <a class="btn btn-danger" href="<?php echo '/administrator/index.php?option=com_rental&task=images.delete&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&unit_id=' . (int) $this->items[0]->unit_id ?>">
            <i class="icon-delete"></i>
            <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_DELETE_IMAGE'); ?>
          </a> 
          <?php if ($i == 0) : ?>
            <span class="icon-default lead pull-right">&nbsp;</span>
          <?php endif; ?>
        </p>

      </div>
    </div>


  </li>
<?php endforeach; ?>

