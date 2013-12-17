<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_feed
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$lang = $app->input->get('lang', 'en');
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
        <?php $property = JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $item->id . '&unit_id=' . (int) $item->unit_id) ?>
        <?php $region = JRoute::_('index.php?option=com_fcsearch&s_kwds=' . $item->alias . '&lang=' . $lang . '&Itemid=165'); ?>

        <div class = "item <?php echo ($leadingcount == 0) ? 'active' : '' ?>">
          <?php if (isset($item->thumbnail) && !empty($item->thumbnail)) : ?>
            
              <a class="" href="<?php echo $property ?>">
                <img src='/images/property/<?php echo $item->unit_id . '/gallery/' . $item->thumbnail ?>' class="img-rounded" />
              </a>
            
         <?php endif; ?>
          <div class="carousel-caption">
              <a class="" href="<?php echo $property ?>"><strong><?php echo $item->unit_title; ?></strong></a>&nbsp;|
              &nbsp;<a href="<?php echo $region ?>"><?php echo htmlspecialchars($item->title); ?></a>
              <?php echo JText::_('MOD_FEATURED_PROPERTY_SLEEPS'); ?>&nbsp;
              <?php echo $item->occupancy; ?>
              <?php if (!empty($item->price)) : ?>|&nbsp;
                &pound;<?php echo $prices['GBP'] ?>
              <?php endif; ?>        
          </div>
        </div>


        <?php $leadingcount++; ?>
      <?php endforeach; ?>
    </div>
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">‹</a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">›</a>
  </div>
<?php endif; ?>
