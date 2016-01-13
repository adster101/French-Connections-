<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$language = JFactory::getLanguage();
$lang = $language->getTag();
$app = JFactory::getApplication();

$Itemid = SearchHelper::getItemid(array('component', 'com_realestate'));

$this->item->itemid = $Itemid;

$input = JFactory::getApplication()->input;
$preview = $input->get('preview', '', 'int');
$append = '';
$user = JFactory::getUser();
$logged_in = ($user->guest) ? false : true;
$uri = JUri::getInstance()->toString();
$link = 'index.php?option=com_realestate&Itemid=' . (int) $Itemid . '&id=' . (int) $this->item->property_id;
$min_prices = JHtml::_('general.price', $this->item->price, $this->item->base_currency, $this->item->exchange_rate_eur, $this->item->exchange_rate_usd);

if ((int) $preview && $preview == 1)
{
  $link .= '&preview=1';
}

$route = JRoute::_($link);

if ((int) $preview && $preview == 1)
{
  $append = '&preview=1';
}

$langs_array = array();

if (!empty($this->item->languages_spoken))
{
  $langs = json_decode($this->item->languages_spoken);

  foreach ($langs as $lang)
  {
    if (!empty($lang))
    {
      $langs_array[] = JText::_($lang);
    }
  }
}
?>

<div class="page-header">
  <h1>
    <?php echo $this->document->title; ?>
  </h1>
  <h3>          
    <?php echo JText::sprintf('COM_REALESTATE_PROPERTY_SUB_TITLE', $this->item->city_title, $this->item->department, number_format($min_prices['GBP']), number_format($min_prices['EUR'])); ?>
  </h3>
</div>
<div class = "navbar-property-navigator" data-spy = "affix" data-offset-top = "640" >
  <div class = "container">
    <div class = "row">
      <div class = "col-lg-10 col-md-9 col-sm-8 hidden-xs">
        <ul class = "nav nav-pills">
          <li>
            <a href = "<?php echo $route ?>#top">
              <span class = "glyphicon glyphicon-home"> </span>&nbsp;
              <?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TOP');
              ?>
            </a>
          </li>
          <li>
            <a href="<?php echo $route ?>#about">
              <span class="glyphicon glyphicon-info-sign"> </span>          
              <?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_DESCRIPTION'); ?>
            </a>
          </li>
          <li>
            <a href="<?php echo $route ?>#email">
              <span class="glyphicon glyphicon-envelope"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_CONTACT'); ?>
            </a>
          </li>
        </ul>
      </div>
      <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="glyphicon-xxlarge visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs-inline-block"> 
          <a href="<?php
          echo 'https://www.facebook.com/dialog/feed?app_id=612921288819888&display=page&href='
          . urlencode($uri)
          . '&redirect_uri='
          . urlencode($uri)
          . '&picture='
          . JURI::root() . 'images/property/'
          . $this->item->realestate_property_id
          . '/thumbs/'
          . urlencode($this->images[0]->image_file_name)
          . '&name=' . urlencode($this->item->unit_title)
          . '&description=' . urlencode(JHtml::_('string.truncate', $this->item->description, 100, true, false));
          ?>">
            <span class="glyphicon social-icon facebook"></span>
          </a> 
          <a target="_blank" href="<?php echo 'http://twitter.com/share?url=' . $uri . '&amp;text=' . $this->escape($this->item->title) ?>" >
            <span class="glyphicon social-icon twitter"></span>
          </a>
          <a target="_blank" href="<?php echo 'https://plus.google.com/share?url=' . $uri ?>">
            <span class="glyphicon social-icon google-plus"></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row" id="main">
  <div class="col-lg-7 col-md-7 col-sm-12">
    <!-- Image gallery -->
    <!-- Needs go into a separate template -->
    <div  role="main">
      <?php if (count($this->images) > 1) : ?>
        <div class="slick-slider">
          <?php foreach ($this->images as $images => $image) : ?> 
            <?php $src = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->property_id . '/gallery/' . $image->image_file_name : JURI::getInstance()->toString(array('scheme')) . $image->url; ?>
            <div>
              <?php if ($images == 0) : ?>
                <img class="img-responsive" data-lazy="<?php echo $src ?>" />
              <?php else: ?>
                <img class="img-responsive" data-lazy="<?php echo $src ?>" />
              <?php endif; ?>
              <p>
                <?php echo $image->caption; ?>
                <span class="muted small">(<?php echo $images + 1 ?> / <?php echo count($this->images) ?>)</span>
              </p>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="carousel-ribbon hidden-xs">
          <?php foreach ($this->images as $images => $image) : ?> 
            <?php $src = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->property_id . '/thumbs/' . $image->image_file_name : JURI::getInstance()->toString(array('scheme')) . $image->url_thumb; ?>
            <div>
              <img src="<?php echo $src ?>" /> 
            </div>     
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <div class="panel panel-default">
          <ul class="slides">
            <?php foreach ($this->images as $images => $image) : ?> 
              <li>
                <img src="<?php echo JURI::root() . 'images/property/' . $this->item->unit_id . '/gallery/' . $image->image_file_name; ?>" />
                <p class="flex-caption">
                  <?php echo $this->escape($image->caption); ?>
                </p>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-lg-5 col-md-5 col-sm-12 key-facts">
    <?php if ((round($this->item->latitude, 2) <> 0) && (round($this->item->longitude, 2) <> 0)) : ?>
      <h3><?php echo JText::_('COM_REALESTATE_PROPERTY_LOCATION'); ?></h3>
      <div id="property_map_canvas" style="width:100%; height:370px;margin-bottom: 9px;" class="clearfix" data-hash="<?php echo JSession::getFormToken() ?>" data-lat="<?php echo $this->escape($this->item->latitude) ?>" data-lon="<?php echo $this->escape($this->item->longitude) ?>"></div>
      <p class="key text-right">
        <span>
          <img src="/images/mapicons/iconflower.png" />&nbsp;<?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_MARKER_KEY', $this->item->property_id) ?>
          &nbsp;&ndash;&nbsp;
          <img src="/images/mapicons/iconplaceofinterest.png" />&nbsp;<?php echo JText::_('COM_ACCOMMODATION_PLACEOFINTEREST_MARKER_KEY') ?>
        </span>
      </p>
    <?php endif; ?>
    <div class="well well-light-blue">
      <?php if ($this->item->price) : ?> 
        <p>
          <strong class="lead">&pound;<?php echo number_format($min_prices['GBP']) ?></strong>
          (&euro;<?php echo number_format($min_prices['EUR']); ?>)
        </p>             
      <?php else: ?>
        <?php echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST'); ?>
      <?php endif; ?>

      <!-- Number of bedrooms, if any -->
      <?php if ($this->item->bedrooms) : ?>
        <p class="dotted">
          <?php echo JText::_('COM_REALESTATE_LISTING_BEDROOMS'); ?>
          <span class="pull-right"><?php echo $this->item->bedrooms; ?></span>
        </p>
      <?php endif; ?>
    
      <!-- Number of bathrooms, if any -->
      <?php if ($this->item->bathrooms) : ?>
        <p class="dotted">
          <?php echo JText::_('COM_REALESTATE_LISTING_BATHROOMS'); ?>
          <span class="pull-right"><?php echo $this->item->bathrooms; ?></span>
        </p>
      <?php endif; ?>
      <!-- Vendor's price notes -->
      <?php if ($this->item->additional_price_notes) : ?>
        <p class='dotted'>
          <?php echo JText::_('COM_REALESTATE_LISTING_ADDITIONAL_PRICE_NOTES'); ?>
          <span class="pull-right">
            <?php echo JText::_($this->item->additional_price_notes); ?>
          </span>
        </p>
      <?php endif; ?>
      <!-- Vendor's price notes -->
      <?php if ($this->item->agency_reference) : ?>
        <p class='dotted'>
          <?php echo JText::_('COM_REALESTATE_LISTING_AGENCY_REFERENCE'); ?>
          <span class="pull-right">
            <?php echo JText::_($this->item->agency_reference); ?>
          </span>
        </p>
      <?php endif; ?>
      <hr/>
      <p class="center">
        <a class="btn btn-primary btn-lg" href="<?php echo JRoute::_('index.php?option=com_realestate&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id); ?>#email">
          <?php echo JText::_('COM_REALESTATE_LISTING_CONTACT_OWNER'); ?>  
        </a>
      </p>
    </div>  
  </div> 
</div>

<div class="row" id="about">
  <div class="col-lg-7 col-md-7 col-sm-7" >
    <?php if ($this->item->title) : ?>
      <h2 class="page-header"><?php echo $this->escape($this->item->title) ?></h2>  
    <?php endif; ?>
    <?php if ($this->item->description) : ?>
      <?php echo $this->item->description; ?>
    <?php endif; ?>
  </div>
</div>





<div class="row" id="email">
  <div class="col-lg-12">
    <?php if ($this->item->title) : ?>
      <h2 class="page-header"><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_EMAIL_THE_OWNER', $this->item->title)) ?></h2> 
    <?php endif; ?>
  </div>
</div>
<div class="row">
  <div class="col-lg-7 col-md-7 col-sm-7">
    <?php echo $this->loadTemplate('form'); ?>
  </div>
  <div class="col-lg-5 col-md-5 col-sm-5">
    <h4><?php echo htmlspecialchars(JText::_('COM_ACCOMMODATION_CONTACT_THE_OWNER')); ?></h4> 
    <?php if ($this->item->use_invoice_details) : ?>
      <?php echo $this->escape($this->item->firstname); ?>&nbsp;<?php echo $this->escape($this->item->surname); ?><br />
    <?php else: ?>
      <?php echo $this->escape($this->item->alt_first_name); ?>&nbsp;<?php echo $this->escape($this->item->alt_surname); ?><br />
    <?php endif; ?>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL'); ?>
      <?php echo ($this->item->use_invoice_details) ? $this->item->phone_1 : $this->item->alt_phone_1; // Assumes there is at least one phone  ?>
    </p>
    <?php if ($this->item->use_invoice_details) : // Show owners second phone number if there is one on the account    ?>
      <?php if (!empty($this->item->phone_2)) : ?>
        <p>
          <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL2'); ?>
          <?php echo $this->item->phone_2; ?>
        </p>
      <?php endif; ?>
    <?php else: // Show the alt second phone number if one has been entered  ?>
      <?php if (!empty($this->item->alt_phone_2)) : ?>
        <p>
          <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL2'); ?>
          <?php echo $this->item->alt_phone_2; ?>
        </p>
      <?php endif; ?>
    <?php endif; ?>
    <?php if (count($langs_array) > 0) : ?>
      <p><?php echo JText::sprintf('COM_ACCOMMODATION_LANGUAGES_SPOKEN', implode(', ', $langs_array)); ?></p>
    <?php endif; ?>
    <?php if ($this->item->website && $this->item->website_visible) : ?>
      <p>
        <?php echo JText::_('COM_ACCOMMODATION_CONTACT_WEBSITE'); ?>
        <a target="_blank" rel="nofollow" href="<?php echo JRoute::_('index.php?option=com_realestate&Itemid=' . (int) $Itemid . '&id=' . (int) $this->item->property_id) . '?' . JSession::getFormToken() . '=1&task=listing.viewsite'; ?>">
          <?php echo JText::_('COM_ACCOMMODATION_CONTACT_WEBSITE_VISIT'); ?>
        </a>
      </p>
    <?php endif; ?>
    <hr />
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_CONTACT_PLEASE_MENTION'); ?>
    </p>
  </div>
</div>
</div>


