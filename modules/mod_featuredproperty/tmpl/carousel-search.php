<?php
/**
 * @package     Joomla.Tutorials
 * @subpackage  Module
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$lang = $app->input->get('lang', 'en');
// Register the Special Offers helper file
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');
$Itemid_property = SearchHelper::getItemid(array('component', 'com_accommodation'));
$Itemid_search = SearchHelper::getItemid(array('component', 'com_fcsearch'));
?>
<script>
  jQuery('#fp-carousel').carousel({
    interval: 4000
  });
  jQuery('.carousel .item').each(function() {
    var next = jQuery(this).next();
    if (!next.length) {
      next = jQuery(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo(jQuery(this));

    for (var i = 0; i < 6; i++) {
      next = next.next();
      if (!next.length) {
        next = jQuery(this).siblings(':first');
      }

      next.children(':first-child').clone().appendTo(jQuery(this));
    }
  });
</script>

<div class="row">
  <div class="carousel slide hidden-xs" id="fp-carousel" data-ride="carousel">
    <div class="carousel-inner">   
      <div class="item active">

        <?php foreach ($items as $key => $item) : ?>
          <?php
          $prices = JHtml::_('general.price', $item->price, $item->base_currency, '', '');
          $description = JHTml::_('string.truncate', $item->description, 75, true, false);
          $title = JText::sprintf('MOD_FEATURED_PROPERTY_THUMB_TITLE', $item->id, $description);
          $region = JRoute::_('index.php?option=com_fcsearch&s_kwds=' . $item->alias . '&lang=' . $lang . '&Itemid=' . (int) $Itemid_search);
          $property = JRoute::_('index.php?option=com_accommodation&Itemid=' . (int) $Itemid_property . '&id=' . (int) $item->id . '&unit_id=' . (int) $item->unit_id);
          ?>
          <?php if ($item->title) : ?>        

            <div class="col-lg-3 col-sm-3"> 
              <p>
                <a title ="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" class="" href="<?php echo $property ?>">
                  <?php if (!empty($item->offer)) : ?>
                    <span class="offer">
                      <?php echo htmlspecialchars($item->offer); ?>
                    </span>
                  <?php endif; ?>
                  <img src='/images/property/<?php echo $item->unit_id . '/thumb/' . $item->thumbnail ?>' class="fp-media-object img-responsive" />
                </a>
              </p>
              <h4 class="fp-media-heading"><a href="<?php echo $region ?>"><?php echo htmlspecialchars($item->title); ?></a></h4>
              <p>
                <?php echo JText::sprintf('MOD_FEATURED_PROPERTY_SLEEPS', $item->occupancy); ?>
                <?php if (!empty($item->price)) : ?> 
                  <?php echo JText::sprintf('MOD_FEATURED_PROPERTY_PRICE_FROM', $prices['GBP']) ?>
                <?php endif; ?><br />
                <?php // echo JHTml::_('string.truncate', $item->description, 25, true, false); ?>
                <a title ="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" class="fp-thumbnail" href="<?php echo $property ?>">
                  <?php echo htmlspecialchars($item->unit_title); ?>&nbsp;&raquo;
                </a>
              </p>     
            </div> 
            <?php if (($key % 4 === 3)) : ?>
            </div><div class="item">
            <?php endif; ?>
          <?php endif; ?>
        <?php endforeach; ?>  
      </div>   
    </div>
    <a class="left carousel-control" href="#fp-carousel" data-slide="prev"><i class="glyphicon glyphicon-chevron-left"></i></a>
    <a class="right carousel-control" href="#fp-carousel" data-slide="next"><i class="glyphicon glyphicon-chevron-right"></i></a>
  </div>
</div>


