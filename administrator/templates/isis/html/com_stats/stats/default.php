<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_stats'); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
  <?php echo JLayoutHelper::render('frenchconnections.search.default', array('view' => $this)); ?>
  <div id="j-main-container" class="row-fluid">
    <?php echo $this->loadTemplate('stats') ?>
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>