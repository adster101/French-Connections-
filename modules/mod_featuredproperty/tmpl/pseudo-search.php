<?php

$Itemid_property = SearchHelper::getItemid(array('component', 'com_accommodation'));
$uri = JUri::getInstance();

$lang = JFactory::getLanguage();

$lang->load('com_fcsearch', JPATH_BASE, null, false, true);
$lang->load('com_accommodation', JPATH_BASE, null, false, true);


?>
<div class="view-search">
<div class="search-results">
  <?php foreach ($items as $key => $item) : ?>
<?php

$route = JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid_property . '&id=' . (int) $item->id . '&unit_id=' . (int) $item->unit_id);
$tagline = JText::sprintf('COM_FCSEARCH_SITE_OCCUPANCY_DETAIL', $item->accommodation_type, $item->property_type, $item->bedrooms, $item->bathrooms, $item->occupancy);
$from_price = JHtmlGeneral::price($item->price, $item->base_currency, '', '');

$description = JHTml::_('string.truncate', $item->description, 75, true, false);
$title = JText::sprintf('COM_FCSEARCH_THUMBNAIL_TITLE', $item->id, $description);
$unit_title = $item->unit_title;
$location_title = $item->location_title;
$property_type = $item->property_type;
$thumb = ($item->thumbnail) ? '/images/property/' . $item->unit_id . '/thumb/' . $item->thumbnail : $uri->getScheme() . '://' . $item->url_thumb;
?>
<div class="search-result">
  <div class="row">
    <div class="col-xs-12 col-sm-9">
      <h3 class="listing-title">
        <a href="<?php echo JRoute::_($route); ?>"><?php echo $unit_title ?></a>
        <small>
          <?php echo $item->property_type . ', ' . $item->title ?>
        </small>
      </h3>
    </div>
    <div class="col-xs-12 col-sm-3">
      <p class="rates">
        <?php if ($item->price) : ?>
            <?php echo JText::_('COM_FCSEARCH_SEARCH_FROM'); ?>
            <span class="lead">
              <?php //echo '&pound;' . $from_price['GBP'] ?>
              <?php echo '&pound;' . round($item->price); ?>
            </span>
            <span class="rate-per">
              <?php echo $item->tariff_based_on; ?>
            </span>
        <?php else : ?>
            <?php echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST'); ?>
        <?php endif; ?>
      </p>
    </div>
  </div>
  <?php if (!empty($item->offer)) : ?>
      <div class="row">
        <div class="col-xs-12">
          <p class="offer">
            <strong><span class="glyphicon glyphicon-tags"></span></strong>&nbsp;
            <?php echo $item->offer; ?>
          </p>
        </div>
      </div>
  <?php endif; ?>
  <div class="row">
    <div class="col-xs-12 col-sm-3">
      <p>
        <a href="<?php echo $route ?>" title ="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
          <?php if (!empty($item->thumbnail)) : ?>
              <img class="img-responsive" src='<?php echo $thumb; ?>' />
          <?php else: ?>
              <img width="210" class="img-responsive" src="<?php echo $uri->getScheme() . '://' . $item->url_thumb ?>" />
          <?php endif; ?>
        </a>
      </p>
    </div>
    <div class="col-xs-12 col-sm-9">
      <div class="row">
        <div class="col-md-9 col-sm-9">
          <p>
            <?php
            echo $tagline;
            echo ($item->changeover_day) ? '&nbsp;' . JText::sprintf('COM_FCSEARCH_CHANGEOVER_DAY', $item->changeover_day) : '';
            echo (!empty($item->distance)) ? JText::sprintf('COM_FCSEARCH_SITE_DISTANCE', (float) $item->distance, $this->escape($location)) : '';
            echo (!empty($item->coast) && ((int) $item->coast)) ? JText::sprintf('COM_FCSEARCH_SITE_DISTANCE_TO_COAST', (float) $item->coast) : '';
            ?>
          </p>
          <p>
            <?php echo JHtml::_('string.truncate', $item->description, 100, true, false); ?>
          </p>
          <p class=""><?php echo JText::sprintf('COM_FCSEARCH_REF', $item->id); ?></p>
        </div>
        <div class="col-md-3 col-sm-3">
          <p class="view-property-button visible-xs-inline-block visible-sm-block visible-md-block visible-lg-block">
            <a href="<?php echo $route ?>" class="btn btn-warning">
              <?php echo JText::_('COM_FCSEARCH_VIEW_PROPERTY') ?>
            </a>
          </p>

          <?php if ($item->reviews) : ?>
              <p class="listing-reviews visible-xs-inline-block visible-xs-inline-block visible-sm-block visible-md-block visible-lg-block">
                <a href="<?php echo $route . '#reviews' ?>">
                  <?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_HAS_NUMBER_OF_REVIEWS', $item->reviews); ?>
                </a>
              </p>
          <?php endif; ?>
          <?php if ($item->is_bookable) : ?>
              <p>
                <span class="glyphicon glyphicon-credit-card lead pull-left"></span>&nbsp;Book online securely
              </p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>
</div>
</div>
