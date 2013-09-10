<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_import'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container" class="span12">
      <?php endif; ?> 
    <fieldset class="adminform">
      <legend><?php echo JText::_('Choose an import file'); ?></legend>
      <p>Import a list of amenities from the #__property_amenities table. Sweet!</p>
      <p>Before running ensure that a list of amenities has been imported into the above table. </p><p>This script runs through all 
        properties and for each determine if there are amenty overrides. If so, they are JSON encoded and added to the 
        local amenities field.</p>
  </div>
  <input type="hidden" name="task" value="" />

  <?php echo JHtml::_('form.token'); ?>
</form>

