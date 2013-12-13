<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

JHtml::_('behavior.caption');
// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
?>
<div class="blog-featured<?php echo $this->pageclass_sfx; ?>">
  <?php if ($this->params->get('show_page_heading') != 0) : ?>
    <div class="page-header">
      <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
      </h1>
    </div>
  <?php endif; ?>
  <?php $leadingcount = 0; ?>
  <?php if (!empty($this->lead_items)) : ?>
    <div id="myCarousel" class="carousel slide"><!-- Carousel items -->
      <ol class="carousel-indicators">
        <?php for ($x = 0; $x < count($this->lead_items); $x++) : ?>
          <li data-target="#myCarousel" data-slide-to="<?php echo $x ?>" class="<?php echo ($x == 0) ? 'active' : '' ?>"></li>
        <?php endfor; ?>
      </ol>
      <div class="carousel-inner">
        <?php foreach ($this->lead_items as &$item) : ?>
          <?php
          $this->item = &$item;
          $this->item_number = $leadingcount;
          echo $this->loadTemplate('item');
          $leadingcount++;
          ?>
        <?php endforeach; ?>
      </div>
      <a class="left carousel-control" href="#myCarousel" data-slide="prev">‹</a>
      <a class="right carousel-control" href="#myCarousel" data-slide="next">›</a>
    </div>
  <?php endif; ?>
</div>
