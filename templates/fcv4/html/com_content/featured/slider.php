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
  <div class="blog-featured<?php echo $this->pageclass_sfx; ?> hidden-xs">
    <?php if ($this->params->get('show_page_heading') != 0) : ?>
      <div class="page-header">
        <h1>
          <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
      </div>
    <?php endif; ?>
    <?php $leadingcount = 0; ?>
    <?php if (!empty($this->lead_items)) : ?>
      <div id="homepageCarousel" class="carousel slide" data-ride="carousel" data-interval="5000"><!-- Carousel items -->

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
        <a class="carousel-left" href="#homepageCarousel" data-slide="prev">
          <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="carousel-right" href="#homepageCarousel" data-slide="next">
          <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
      </div>
    <?php endif; ?>
  </div>
