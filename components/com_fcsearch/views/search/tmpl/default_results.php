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

if ($this->total == 0):
  ?>
  <div id="search-result-empty">
    <h2><?php echo JText::_('COM_FINDER_SEARCH_NO_RESULTS_HEADING'); ?></h2>
    <?php if ($app->getLanguageFilter()) : ?>
      <p><?php echo JText::sprintf('COM_FINDER_SEARCH_NO_RESULTS_BODY_MULTILANG', $this->escape($this->query->input)); ?></p>
    <?php else : ?>
      <p><?php echo JText::sprintf('COM_FINDER_SEARCH_NO_RESULTS_BODY', $this->escape($this->query->input)); ?></p>
    <?php endif; ?>
  </div>


  <?php
else:
  // Prepare the pagination string.  Results X - Y of Z
  $start = (int) $this->pagination->get('limitstart') + 1;
  $total = (int) $this->pagination->get('total');
  $limit = (int) $this->pagination->get('limit') * $this->pagination->pagesTotal;
  $limit = (int) ($limit > $total ? $total : $limit);
  $pages = JText::sprintf('COM_FINDER_SEARCH_RESULTS_OF', $start, $limit, $total);
  ?>

  <div class="search-pagination">
    <div class="pagination">
      <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
    <div class="search-pages-counter">
      <?php echo $pages; ?>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span9">
      <ul class="search-results<?php echo $this->pageclass_sfx; ?> list-striped">
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
  </div>

  <div class="search-pagination">
    <div class="pagination">
      <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
    <div class="search-pages-counter">
      <?php echo $pages; ?>
    </div>
  </div>
<?php endif; ?>


