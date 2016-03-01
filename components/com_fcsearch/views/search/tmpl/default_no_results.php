<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="container">
  <p class='lead'>
    <strong><?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_HEADING'); ?></strong>
  </p>
  <?php if ($this->alt): ?>
      <p><strong>Did you mean?</strong></p>
      <?php foreach ($this->alt as $key => $value) : ?>
          <p><a href="<?php echo 'accommodation/' . $value->alias ?>"><em><?php echo $value->title ?></em></a></p>
      <?php endforeach; ?>
  <?php else: ?>
      <p>
        <?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_BODY'); ?>
      </p>
  <?php endif; ?>
  <?php
// Load the most popular search module 
  $module = JModuleHelper::getModule('mod_popular_search');
  echo JModuleHelper::renderModule($module);
  ?>

</div>
