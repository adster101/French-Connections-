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

// Add an additional item to the FPs for the 'promo' slot
$item = new stdClass();
$item->promo = true;
$item->description = JText::_('MOD_FEATURED_PROPERTY_ADVERTISE_HERE');
array_push($items, $item);

// Chunk up the items array
$chunks = array_chunk($items, 4);
$uri = JUri::getInstance();

?>
<div class="row">
  <div class="carousel slide hidden-xs" id="fp-carousel" data-ride="carousel">
    <ol class="carousel-indicators">   
      <?php if (count($chunks) > 1) : ?>
          <?php foreach ($chunks as $key => $item) : ?>
              <li data-target="#fp-carousel" data-slide-to="<?php echo $key ?>" <?php echo ($key == 0) ? 'class="active"' : ''; ?>></li>
          <?php endforeach; ?>
      <?php endif; ?>
    </ol>
    <div class="carousel-inner">
      <?php foreach ($chunks as $key => $chunk) : ?>
          <div class="item <?php echo ($key == 0) ? 'active' : ''; ?>">
            <?php foreach ($chunk as $item) : ?>
                <?php
                $prices = JHtml::_('general.price', $item->price, $item->base_currency, '', '');
                $description = JHTml::_('string.truncate', $item->description, 75, true, false);
                $title = JText::sprintf('MOD_FEATURED_PROPERTY_THUMB_TITLE', $item->id, $description);
                $region = JRoute::_('index.php?option=com_fcsearch&s_kwds=' . $item->alias . '&lang=' . $lang . '&Itemid=' . (int) $Itemid_search);
                $property = JRoute::_('index.php?option=com_accommodation&Itemid=' . (int) $Itemid_property . '&id=' . (int) $item->id . '&unit_id=' . (int) $item->unit_id);
                $thumb = ($item->thumbnail) ? '/images/property/' . $item->unit_id . '/thumb/' . $item->thumbnail : $uri->getScheme() . '://' . $item->url_thumb;
                ?>
                <?php if ($item->title) : ?>
                    <div class="col-lg-3 col-sm-3">
                      <p>
                        <a title ="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" class="view-search-fp-link" href="<?php echo $property ?>">
                          <?php if (!empty($item->offer)) : ?>
                              <span class="offer small" data-toggle="tooltip" title="<?php echo htmlspecialchars($item->offer) ?>">
                                <?php echo JHtml::_('string.truncate', htmlspecialchars($item->offer), 35, true, false); ?>
                              </span>
                          <?php endif; ?>
                          <img src='<?php echo $thumb ?>' class="fp-media-object img-responsive" />
                        </a>
                      </p>
                      <h4 class="fp-media-heading">
                        <a href="<?php echo $region ?>"><?php echo htmlspecialchars($item->title); ?></a>
                      </h4>
                      <p>
                        <?php echo JText::sprintf('MOD_FEATURED_PROPERTY_SLEEPS', $item->occupancy); ?>
                        <?php if (!empty($item->price)) : ?>
                            <?php echo JText::sprintf('MOD_FEATURED_PROPERTY_PRICE_FROM', $prices['GBP']) ?>
                        <?php endif; ?><br />
                        <?php // echo JHTml::_('string.truncate', $item->description, 25, true, false); ?>
                        <a title ="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" class="fp-thumbnail view-search-fp-link" href="<?php echo $property ?>">
                          <?php echo htmlspecialchars($item->unit_title); ?>&nbsp;&raquo;
                        </a>
                      </p>
                    </div>
                <?php elseif ($item->promo) : ?>
                    <div class="col-lg-3 col-sm-3"> 
                      <p>
                        <img src='/images/general/logo-5.png' class="fp-media-object img-responsive" />
                      </p>
                      <h4 class="fp-media-heading">
                        Advertise here
                      </h4>
                      <p><?php echo $item->description ?></p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
          </div>
      <?php endforeach; ?>
    </div> 
    <a class="left carousel-control" href="#fp-carousel" data-slide="prev">
      <i class="glyphicon glyphicon-chevron-left"></i>
    </a>
    <a class="right carousel-control" href="#fp-carousel" data-slide="next">
      <i class="glyphicon glyphicon-chevron-right"></i>
    </a>
  </div>
</div>



