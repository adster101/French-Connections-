<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

// Activate the highlighter if enabled.
if (!empty($this->query->highlight) && $this->params->get('highlight_terms', 1)) {
  JHtml::_('behavior.highlighter', $this->query->highlight);
}

// Get the application object.
$app = JFactory::getApplication();

?>
  <div class="row-fluid">
    <div class="span9">
      <ul class="search-results list-striped">
        <?php
        for ($i = 0, $n = count($this->results); $i < $n; $i++):
          $this->result = &$this->results[$i];
          ?>
          <?php echo $this->loadTemplate('result'); ?>
          <?php
        endfor;
        ?>
      </ul>
    </div>
    <div class="span3">
      <?php echo $this->loadTemplate('refine_main'); ?>     
    </div>
  </div>

  <div class="search-pagination">
    <div class="pagination">
      <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
    <div class="search-pages-counter">
      <?php echo $pages; ?>
    </div>
  </div>

