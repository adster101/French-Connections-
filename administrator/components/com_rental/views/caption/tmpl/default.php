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
  <?php if (empty($this->item->url)) : ?>
    <img class="media-object" src="<?php echo '/images/property/' . (int) $this->item->unit_id . '/thumb/' . $this->escape($this->item->image_file_name); ?>" />
  <?php else: ?>
    <img class="media-object" src="<?php echo 'http://' . $this->item->url ?>" /
  <?php endif; ?>
</p>
<form action="<?php echo JRoute::_('index.php?option=com_rental'); ?>" method="post" name="captionForm" id="captionForm" class="form-validate">
  <div class="control-group">
    <?php echo $this->form->getLabel('caption'); ?>
    <div class="controls">
      <?php echo $this->form->getInput('caption'); ?>
    <p class="caption">
    <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_REMAINING_CHARS_CAPTION'); ?>
  </p>      
    </div>
  </div>
 
  <?php echo JHtml::_('form.token'); ?>
  <?php echo $this->form->getInput('unit_id'); ?>
  <?php echo $this->form->getInput('id'); ?>

  <input type="hidden" name="task" value="" />
  <div class='pull-right'>
    <?php echo JToolBar::getInstance('actions')->render(); ?>
  </div>
</form>
