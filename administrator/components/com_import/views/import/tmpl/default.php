<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('bootstrap.tooltip');
?>
<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container" class="span12">
      <?php endif; ?> 
        <h4>Import data</h4>
      <p>Choose from the menu which data set you wish to import.</p>
      <p>Bear the following point in mind when importing.</p>
      <ul>
        <li>Ensure that target table is empty prior to import.</li>
        <li>Imports can take a long time. Locations take about half an hour.</li>
        <li>You may need to import other supplemental data directly via mysql csv import.</li>
      </ul>
    </div>
  </div>
