<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.tooltip');
?>

    <?php foreach ($this->items as $i => $item): ?>
      <tr>
        <td class="hide">
          <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td>
          <input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="text-area-order hidden-desktop hidden-phone hidden-tablet" />
          <div>
            <?php echo $this->pagination->orderUpIcon($i, true, 'images.orderup', 'JLIB_HTML_MOVE_UP', 1); ?>
          <?php echo $this->pagination->orderDownIcon($i, count($this->items), true, 'images.orderdown', 'JLIB_HTML_MOVE_DOWN', 1); ?>
          </div>
        </td>
        <td class="thumbnail-default" align="center">
          <?php if ($i == 0) : ?>
            <p class="center"><span class="icon-default lead">&nbsp;</span></p>
          <?php endif; ?>
        </td>
        <td>
          <img src="<?php echo '/images/property/' . (int) $item->unit_id . '/thumb/' . $item->image_file_name; ?>" />
        </td>
        <td class="caption">
          <input  class="input input-xlarge" type="text" name="jform[caption]" value="<?php echo $this->escape($item->caption); ?>" maxlength="75" />
          <a style="margin-bottom:9px;" class="btn btn-primary update-caption" href="<?php echo '/administrator/index.php?option=com_rental&task=images.updatecaption&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&unit_id=' . (int) $this->items[0]->unit_id ?>" >
            <i class="icon-pencil-2 icon-white"></i>
            <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_UPDATE_CAPTION'); ?>
          </a>
          <p class="muted"><?php echo Jtext::_('COM_RENTAL_HELLOWORLD_REMAINING_CHARS_CAPTION'); ?></p>
        </td>
        <td>
          <a class="btn btn-danger delete" href="<?php echo '/administrator/index.php?option=com_rental&task=images.delete&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&unit_id=' . (int) $this->items[0]->unit_id ?>">
            <i class="icon-delete"></i>
            <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_DELETE_IMAGE'); ?>
          </a>
        </td>

      </tr>
    <?php endforeach; ?>
