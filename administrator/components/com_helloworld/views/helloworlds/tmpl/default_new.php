<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

?>

<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING'); ?></h3>
  </div>
  <?php if (count($this->items) > 0) : ?>    
    <div class="modal-body">
      <!-- Fill me in -->
      
    </div>
    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_HELLOWORLD_NEW_PROPERTY_CANCEL'); ?></button>
      <button class="btn btn-primary">
        <?php echo JText::_('COM_HELLOWORLD_NEW_PROPERTY_PROCEED'); ?>
        <i class="boot-icon-forward boot-icon-white"></i>
      </button>
      </form>
    </div>

  <?php else : ?>
    <div class="modal-header">
      <div class="pre_message">
        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING_FIRST_PROPERTY_BLURB'); ?>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_HELLOWORLD_NEW_PROPERTY_CANCEL'); ?></button>

      <a class="btn btn-primary" href="index.php?option=com_helloworld&task=helloworld.edit">
        <?php echo JText::_('COM_HELLOWORLD_NEW_PROPERTY_PROCEED'); ?>
        <i class="boot-icon-forward boot-icon-white"></i>
      </a>
    </div>
  <?php endif; ?>


</div>