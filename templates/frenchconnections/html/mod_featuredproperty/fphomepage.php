<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_feed
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>


<?php $leadingcount = 0; ?>
<?php if (!empty($items)) : ?>
  <div id="myCarousel" class="carousel slide"><!-- Carousel items -->
    <ol class="carousel-indicators">
      <?php for ($x = 0; $x < count($items); $x++) : ?>
        <li data-target="#myCarousel" data-slide-to="<?php echo $x ?>" class="<?php echo ($x == 0) ? 'active' : '' ?>"></li>
      <?php endfor; ?>
    </ol>
    <div class="carousel-inner">
      <?php foreach ($items as $item) : ?>
        <?php $prices = JHtml::_('general.price', $item->price, $item->base_currency, '', ''); ?>

        <div class = "item <?php echo ($leadingcount == 0) ? 'active' : '' ?>">
          <?php if (isset($item->thumbnail) && !empty($item->thumbnail)) : ?>
            <p>
              <a class="" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $item->id . '&unit_id=' . (int) $item->unit_id) ?>">
                <img src='/images/property/<?php echo $item->unit_id . '/gallery/' . $item->thumbnail ?>' class="img-rounded" />
              </a>
            </p>            <?php endif; ?>
          <div class="">
            <h4 class="item-title">
              <?php echo htmlspecialchars($item->title); ?>
            </h4>


            <p>
              <strong><?php echo $item->title; ?></strong> | 
              <?php echo JText::_('MOD_FEATURED_PROPERTY_SLEEPS'); ?>
              <?php echo $item->occupancy; ?>
              <?php if (!empty($item->price)) : ?> |
                &pound; <?php echo $prices['GBP'] ?>
              <?php endif; ?>
            </p>
          </div>
        </div>


        <?php $leadingcount++; ?>
      <?php endforeach; ?>
    </div>
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">‹</a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">›</a>
  </div>
<?php endif; ?>
