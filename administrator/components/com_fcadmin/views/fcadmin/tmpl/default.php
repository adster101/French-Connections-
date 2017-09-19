<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('bootstrap.tooltip');
?>
<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span4">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span8">
    <?php else : ?>
      <div id="j-main-container" class="span12">
      <?php endif; ?> 
        <?php echo JText::_('COM_FCADMIN_ADMIN_BLURB'); ?>
    </div>
  </div>
