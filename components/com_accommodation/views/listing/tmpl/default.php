<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$language = JFactory::getLanguage();
$lang = $language->getTag();
$app = JFactory::getApplication();

$Itemid = SearchHelper::getItemid(array('component', 'com_accommodation'));
$HolidayMakerLogin = SearchHelper::getItemid(array('component', 'com_users'));

$this->item->itemid = $Itemid;

$input = JFactory::getApplication()->input;
$preview = $input->get('preview', '', 'int');
$append = '';
$user = JFactory::getUser();
$logged_in = ($user->guest) ? false : true;
$uri = JUri::getInstance()->toString();

$action = (array_key_exists($this->item->unit_id, $this->shortlist)) ? 'remove' : 'add';
$inShortlist = (array_key_exists($this->item->unit_id, $this->shortlist)) ? 1 : 0;
$link = 'index.php?option=com_accommodation&Itemid=' . (int) $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id;

// TO DO - Should also add a
$owner = JFactory::getUser($this->item->created_by)->username;

if ((int) $preview && $preview == 1)
{
  $link .= '&preview=1';
}

$route = JRoute::_($link);

if ((int) $preview && $preview == 1)
{
  $append = '&preview=1';
}

$price_range = array();
if (!empty($this->tariffs))
{
  foreach ($this->tariffs as $tariff)
  {
    $price_range[] = $tariff->tariff;
  }
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

$amenities = ($this->item->local_amenities) ? json_decode($this->item->local_amenities) : array();

// Shortlist button thingy
$shortlist = new JLayoutFile('frenchconnections.general.shortlist');
$displayData = new StdClass;
$displayData->action = $action;
$displayData->inShortlist = $inShortlist;
$displayData->unit_id = $this->item->unit_id;
$displayData->class = ' btn btn-default';

// Add the reviews to item for the above layout.
// TO DO - refactor so that $this->item contains all elements of the listing for use in layouts?
$this->item->reviews = $this->reviews;

// Include the content helper so we can get the route of the success article
require_once JPATH_SITE . '/components/com_content/helpers/route.php';

// Register the general helper class
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');

$min_prices = (!empty($this->tariffs)) ? JHtmlGeneral::price(min($price_range), $this->item->base_currency, $this->item->exchange_rate_eur, $this->item->exchange_rate_usd) : '';
$max_prices = (!empty($this->tariffs)) ? JHtmlGeneral::price(max($price_range), $this->item->base_currency, $this->item->exchange_rate_eur, $this->item->exchange_rate_usd) : '';

$crumbs = JModuleHelper::getModules('breadcrumbs-tmp'); //If you want to use a different position for the modules, change the name here in your override.  
$mpu = JModuleHelper::getModules('property-mpu'); //If you want to use a different position for the modules, change the name here in your override.  

?>

<div class="container">
  <h1 class="page-header">
    <?php echo $this->document->title; ?>
  </h1>
  
  <!-- Begin breadcrumbs -->
  <?php foreach ($crumbs as $module) : // Render the cross-sell modules etc   ?>
    <?php echo JModuleHelper::renderModule($module, array('style' => 'no', 'id' => 'section-box')); ?>
  <?php endforeach; ?>
  <!-- End breadcrumbs -->
  <?php if (count($this->units) > 1) : ?>
    <?php echo $this->loadTemplate('units'); ?>
  <?php endif; ?>
</div>
<div class="navbar-property-navigator" data-spy="affix" data-offset-top="640" >
  <div class="container">
    <div class="row">
      <div class="col-lg-10 col-md-9 col-sm-8 hidden-xs">
        <ul class="nav nav-pills">
          <li>
            <a href="<?php echo $route ?>#top">
              <span class="glyphicon glyphicon-home"> </span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TOP'); ?>
            </a>
          </li>
          <li>
            <a href="<?php echo $route ?>#about">
              <span class="glyphicon glyphicon-info-sign"> </span>          
              <?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_DESCRIPTION'); ?>
            </a>
          </li>
          <?php if (!empty($this->item->location_details)) : ?>
            <li>
              <a href="<?php echo $route ?>#location">
                <span class="glyphicon glyphicon-map-marker"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_LOCATION'); ?>
              </a>
            </li>
          <?php endif; ?>
          <?php if (!empty($this->item->getting_there)) : ?>
            <li>
              <a href="<?php echo $route ?>#gettingthere">
                <span class="glyphicon glyphicon-plane"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TRAVEL'); ?>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($this->item->reviews && count($this->item->reviews) > 0) : ?>
            <li>
              <a href="<?php echo $route ?>#reviews">
                <span class="glyphicon glyphicon-power-cord "></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_REVIEWS'); ?>
              </a>
            </li>
          <?php endif; ?>
          <li>
            <a href="<?php echo $route ?>#facilities">
              <span class="glyphicon glyphicon-power-cord"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_FACILITIES'); ?>
            </a>
          </li>
          <li>
            <a href="<?php echo $route ?>#availability">
              <span class="glyphicon glyphicon-calendar"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_AVAILABILITY'); ?>
            </a>
          </li>
          <li>
            <a href="<?php echo $route ?>#tariffs">
              <span class="glyphicon glyphicon-credit-card"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TARIFFS'); ?>
            </a>
          </li>
          <li>
            <a href="<?php echo $route ?>#email">
              <?php $contact_anchor_label = ($this->item->is_bookable) ? 'COM_ACCOMMODATION_NAVIGATOR_BOOK_NOW' : 'COM_ACCOMMODATION_NAVIGATOR_CONTACT'; ?>
              <span class="glyphicon glyphicon-envelope"></span>&nbsp;<?php echo JText::_($contact_anchor_label); ?>
            </a>
          </li>
        </ul>
      </div>
      <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs-inline-block">
          <?php if ($logged_in) : ?>
            <?php echo $shortlist->render($displayData); ?>
          <?php else : ?>
            <a class="btn btn-default" href="<?php echo JRoute::_('index.php?option=com_users&Itemid=' . (int) $HolidayMakerLogin) ?>">
              <span class="glyphicon glyphicon-heart muted"></span>
              <?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?>
            </a>    
          <?php endif; ?>
        </div>
        <div class="glyphicon-xxlarge visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs-inline-block"> 
          <a target="_blank" href="<?php
          echo 'https://www.facebook.com/dialog/feed?app_id=612921288819888&display=page&href='
          . urlencode($uri)
          . '&redirect_uri='
          . urlencode($uri)
          . '&picture='
          . JURI::root() . 'images/property/'
          . $this->item->unit_id
          . '/thumbs/'
          . urlencode($this->images[0]->image_file_name)
          . '&name=' . urlencode($this->item->unit_title)
          . '&description=' . urlencode(JHtml::_('string.truncate', $this->item->description, 100, true, false));
          ?>"
             <span class="glyphicon social-icon facebook"></span>
          </a> 
          <a target="_blank" href="<?php echo 'http://twitter.com/share?url=' . $uri . '&amp;text=' . $this->escape($this->item->unit_title) ?>" >
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
<div class="container">
  <?php if (count($this->offer)) : ?>
    <div class="well well-small special-offer">
      <h4>   
        <?php echo $this->escape($this->offer->title); ?>
      </h4>
      <p>
        <?php echo $this->escape($this->offer->description); ?>  
      </p>
      <p>
        <?php echo JText::sprintf('COM_ACCOMMODATION_OFFER_ENDS', $this->offer->days, $route . '#email'); ?>
      </p>
    </div>
  <?php endif; ?>

  <div class="row" id="main">
    <div class="col-lg-7 col-md-7 col-sm-12">
      <?php echo $this->loadTemplate('gallery'); ?>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-12 key-facts">
      <div class="well well-light-blue">
        <?php if ($this->tariffs) : ?> 
          <?php if (min($price_range) == max($price_range)) : ?>
            <p>
              <strong class="lead">&pound;<?php echo $min_prices['GBP'] ?></strong>
              <?php if ($this->item->tariffs_based_on) : ?>
                <?php echo '&nbsp;' . htmlspecialchars($this->item->tariffs_based_on); ?>
              <?php endif; ?>
              <br />
              (&euro;<?php echo $min_prices['EUR']; ?>)
            </p>             
          <?php else: ?>
            <p>
              <strong class="lead">&pound;<?php echo $min_prices['GBP'] . ' - &pound;' . $max_prices['GBP']; ?></strong>
              <?php if ($this->item->tariffs_based_on) : ?>
                <?php echo '&nbsp;' . htmlspecialchars($this->item->tariffs_based_on); ?>
              <?php endif; ?>
              <br />
              (&euro;<?php echo $min_prices['EUR'] . ' - &euro;' . $max_prices['EUR']; ?>)
            </p> 
          <?php endif; ?>
        <?php else: ?>
          <?php echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST'); ?>
        <?php endif; ?>
        <!-- Max capacity/occupancy -->
        <?php if ($this->item->occupancy) : ?>
          <p class="dotted">
            <?php echo JText::_('COM_ACCOMMODATION_SITE_OCCUPANCY'); ?>
            <span class="pull-right"><?php echo $this->item->occupancy; ?></span>
          </p>
        <?php endif; ?>
        <!-- Number of bedrooms, if any -->
        <?php if ($this->item->bedrooms) : ?>
          <p class="dotted">
            <?php echo JText::_('COM_ACCOMMODATION_SITE_BEDROOMS'); ?>
            <span class="pull-right"><?php echo $this->item->bedrooms; ?></span>
          </p>
        <?php endif; ?>
        <!-- Number of bathrooms, if any -->
        <?php if ($this->item->bathrooms) : ?>
          <p class="dotted">
            <?php echo JText::_('COM_ACCOMMODATION_SITE_BATHROOMS'); ?>
            <span class="pull-right"><?php echo $this->item->bathrooms; ?></span>
          </p>
        <?php endif; ?>
        <!-- Number of separate toilets, if any -->
        <?php if ($this->item->toilets) : ?>
          <p class="dotted">
            <?php echo JText::_('COM_ACCOMMODATION_SITE_TOILETS'); ?>
            <span class="pull-right"><?php echo $this->item->toilets; ?></span>
          </p>
        <?php endif; ?>
        <!-- Distance to coast -->
        <?php if ($this->item->distance_to_coast) : ?>
          <p class="dotted">
            <?php echo JText::_('COM_ACCOMMODATION_SITE_DISTANCE_TO_COAST'); ?>
            <span class="pull-right"><?php echo $this->item->distance_to_coast; ?></span>
          </p>
        <?php endif; ?>
        <!-- Changeover day -->
        <?php if ($this->item->changeover_day) : ?>
          <p class="dotted">
            <?php echo JText::_('COM_ACCOMMODATION_CHANGEOVER_DAY'); ?>
            <span class="pull-right"><?php echo $this->item->changeover_day; ?></span>
          </p>
        <?php endif; ?>
        <!-- Accommodation type -->
        <?php if ($this->item->accommodation_type) : ?>
          <p class="dotted">
            <?php echo JText::_('COM_ACCOMMODATION_SITE_ACCOMMODATION_TYPE'); ?>
            <span class="pull-right"><?php echo $this->item->accommodation_type; ?></span>
          </p>
        <?php endif; ?>
        <!-- External facilities inc pool type-->
        <?php if (array_key_exists('External Facilities', $this->unit_facilities) || (array_key_exists('Suitability', $this->unit_facilities))) : ?>
          <p class=" clearfix">
            <?php if (array_key_exists('External Facilities', $this->unit_facilities)) : ?> 
              <span>
                <strong><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_EXTERNAL'); ?></strong>
                <?php echo implode(', ', $this->unit_facilities['External Facilities']) ?>
              </span>
            <?php endif; ?>  
          </p>
          <p>
            <?php if (array_key_exists('Suitability', $this->unit_facilities)) : ?>
              <span>
                <strong><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_SUITABILITY'); ?></strong>
                <?php echo implode(', ', $this->unit_facilities['Suitability']) ?>
              </span>         
            <?php endif; ?>      
          </p>   
        <?php endif; ?>
        <!-- LWL Bullshine -->
        <?php if ($this->item->lwl) : ?>
          <p class="">
            <?php echo Jtext::_('COM_ACCOMMODATION_THIS_PROPERTY_OFFERS_LONG_WINTER_LETS'); ?>
          </p>
        <?php endif; ?>
        <!-- END LWL Bullshine -->

        <?php echo $this->loadTemplate('reviews'); ?>

        <p class="center">
          <a class="btn btn-info btn-lg"  id="availability" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . $append); ?>#availability">
            <?php echo JText::_('COM_ACCOMMODATION_SITE_CHECK_AVAILABILITY'); ?>  
          </a>
          <a class="btn btn-primary btn-lg"  id="enquiry" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . $append); ?>#email">
            <?php echo ($this->item->is_bookable) ? JText::_('COM_ACCOMMODATION_SITE_BOOK_NOW') : JText::_('COM_ACCOMMODATION_SITE_CONTACT_OWNER'); ?>  
          </a>
        </p>
      </div>  
    </div> 
  </div>

  <div class="row" id="about">
    <div class="col-lg-7 col-md-7 col-sm-7" >
      <?php if ($this->item->unit_title) : ?>
        <h2 class="page-header"><?php echo $this->escape($this->item->unit_title) ?></h2>  
      <?php endif; ?>
      <?php if ($this->item->description) : ?>
        <?php echo $this->item->description; ?>
      <?php endif; ?>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5">
      <?php
      jimport('joomla.application.module.helper');
      $modules = JModuleHelper::getModule('mod_OpenX_spc', 'MPU-LISTING');
      $attribs['style'] = 'html5';
      echo JModuleHelper::renderModule($modules, $attribs);
      ?>
    </div>
  </div>
  <?php if (!empty($this->item->location_details)) : ?>
    <div class="row" id="location">
      <div class="col-lg- col-md-7 col-sm-12">
        <?php if ($this->item->unit_title) : ?>
          <h2 class="page-header"><?php echo JText::sprintf('COM_ACCOMMODATION_ABOUT_ACCOMMODATION_IN', $this->item->city, $this->item->department, $this->item->region) ?></h2>  
        <?php endif; ?>
        <?php if ($this->item->location_details) : ?>
          <?php echo $this->item->location_details; ?>
        <?php endif; ?> 
        <?php if (!empty($amenities)) : ?>
          <h4>Local amenities</h4>
          <?php foreach ($amenities as $k => $v) : ?>
            <p><strong><?php echo JText::_('COM_ACCOMMODATION_' . $this->escape(strtoupper($k))); ?></strong>
              <?php echo JString::ucwords($this->escape($v)); ?></p>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div class="col-lg-5 col-md-5 col-sm-12">   
        <h2 class="page-header"><?php echo JText::sprintf('COM_ACCOMMODATION_ABOUT_ON_THE_MAP_IN', $this->item->city) ?></h2>  

        <div id="property_map_canvas" style="width:100%; height:370px;margin-bottom: 9px;" class="clearfix" data-hash="<?php echo JSession::getFormToken() ?>" data-lat="<?php echo $this->escape($this->item->latitude) ?>" data-lon="<?php echo $this->escape($this->item->longitude) ?>"></div>
        <p class="key text-right">
          <span>
            <img src="/images/mapicons/iconflower.png" />&nbsp;<?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_MARKER_KEY', $this->item->property_id) ?>
            &nbsp;&ndash;&nbsp;
            <img src="/images/mapicons/iconplaceofinterest.png" />&nbsp;<?php echo JText::_('COM_ACCOMMODATION_PLACEOFINTEREST_MARKER_KEY') ?>
          </span>
        </p>
      </div>
    </div>
  <?php endif; ?>

  <?php if (!empty($this->item->getting_there)) : ?>
    <div class="row" id="gettingthere">
      <div class="col-lg-7 col-md-7 col-sm-12">
        <?php if ($this->item->unit_title) : ?>
          <h2 class="page-header" ><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_HOW_TO_GET_TO_ACCOMMODATION_IN', $this->item->unit_title)) ?></h2>  
        <?php endif; ?>
        <?php if ($this->item->getting_there) : ?>
          <?php echo $this->item->getting_there; ?>
        <?php endif; ?>
        <!-- Access options -->
        <?php if (array_key_exists('Location access', $this->property_facilities)) : ?>
          <h4><?php echo JText::_('COM_ACCOMMODATION_SITE_ACCESS_OPTIONS') ?></h4>
          <p><?php echo implode(', ', $this->property_facilities['Location access']) ?></p>         
        <?php endif; ?>
        <h4><?php echo JText::_('COM_ACCOMMODATION_NEAREST_AIRPORT') ?></h4>
        <p>
          <?php $airport_route = JRoute::_(ContentHelperRoute::getArticleRoute((int) $this->item->airport_id)); ?>
          <?php echo Jtext::sprintf('COM_ACCOMMODATION_NEAREST_AIRPORT_DETAIL', $airport_route, $this->item->airport, $this->item->airport_code) ?>
        </p>
      </div>
      <div class="col-lg-5 col-md-5 col-sm-12"> 

      </div>
    </div>
  <?php endif; ?>


  <?php if ($this->reviews) : ?>

    <div class="row" id="reviews">
      <div class="col-lg-7 col-md-7 col-sm-7">
        <?php if ($this->item->unit_title) : ?>
          <h2 class="page-header"><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_REVIEWS_AT', $this->item->unit_title)) ?></h2> 
        <?php endif; ?>
        <div class="well well-sm well-light-blue">
          <?php foreach ($this->reviews as $review) : ?>
            <figure>
              <blockquote class="quote">
                <?php echo strip_tags($review->review_text, '<p>,<br>'); ?> 
                <?php echo JHtmlProperty::rating($review->rating); ?>
              </blockquote>  
              <figcaption>
                <cite>  
                  <?php echo $review->guest_name; ?>
                  <?php echo JFactory::getDate($review->date)->calendar('D, d M Y'); ?>
                </cite> 
              </figcaption>
            </figure>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-5 col-md-5 col-sm-5"></div>
    </div>
  <?php endif; ?>
  <div class="row" id="facilities">
    <div class="col-lg-7 col-md-7 col-sm-7">
      <?php if ($this->item->unit_title) : ?>
        <h2 class="page-header"><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_FACILITIES_AT', $this->item->unit_title)) ?></h2> 
      <?php endif; ?>
      <table class="table table-striped">
        <tbody>
          <?php if ($this->item->property_type && $this->item->accommodation_type) : ?>
            <tr>
              <td><?php echo JText::_('COM_ACCOMMODATION_SITE_PROPERTY_TYPE') ?></td>
              <td><?php echo $this->item->property_type . ' (' . $this->item->accommodation_type . ')'; ?></td>        
            </tr>
          <?php endif; ?>

          <?php if ($this->item->bathrooms) : ?>    
            <tr>
              <td><?php echo JText::_('COM_ACCOMMODATION_SITE_BATHROOMS') ?></td>
              <td><?php echo $this->item->bathrooms; ?></td>        
            </tr>
          <?php endif; ?>

          <!-- Bedrooms -->
          <?php
          if ($this->item->bedrooms && $this->item->bedrooms > 0)
          {
            ?>    
            <tr>
              <td><?php echo JText::_('COM_ACCOMMODATION_SITE_BEDROOMS') ?></td>
              <td>
                <?php
                if ($this->item->single_bedrooms) : echo JText::sprintf('COM_ACCOMMODATION_SITE_SINGLE_BEDROOMS', $this->item->single_bedrooms);
                endif;
                ?>
                <?php
                if ($this->item->double_bedrooms) : echo JText::sprintf('COM_ACCOMMODATION_SITE_DOUBLE_BEDROOMS', $this->item->double_bedrooms);
                endif;
                ?>
                <?php
                if ($this->item->triple_bedrooms) : echo JText::sprintf('COM_ACCOMMODATION_SITE_TRIPLE_BEDROOMS', $this->item->triple_bedrooms);
                endif;
                ?>
                <?php
                if ($this->item->quad_bedrooms) : echo JText::sprintf('COM_ACCOMMODATION_SITE_QUAD_BEDROOMS', $this->item->quad_bedrooms);
                endif;
                ?>
                <?php
                if ($this->item->twin_bedrooms) : echo JText::sprintf('COM_ACCOMMODATION_SITE_TWIN_BEDROOMS', $this->item->twin_bedrooms);
                endif;
                ?>
                <?php
                if ($this->item->extra_beds) : echo JText::sprintf('COM_ACCOMMODATION_SITE_EXTRA_BEDROOMS', $this->item->extra_beds);
                endif;
                ?>
                <?php
                if ($this->item->childrens_beds) : echo JText::sprintf('COM_ACCOMMODATION_SITE_CHILDRENS_BEDS', $this->item->childrens_beds);
                endif;
                ?>
                <?php
                if ($this->item->cots) : echo JText::sprintf('COM_ACCOMMODATION_SITE_COTS', $this->item->cots);
                endif;
                ?>
              </td>        
            </tr>
          <?php } ?>

          <!-- Occupancy -->
          <?php if ($this->item->occupancy) : ?>
            <tr>
              <td><?php echo JText::_('COM_ACCOMMODATION_SITE_OCCUPANCY') ?></td>
              <td><?php echo $this->item->occupancy; ?></td>        
            </tr>
          <?php endif; ?>

          <!-- Suitability -->
          <?php if (array_key_exists('Suitability', $this->unit_facilities)) : ?>
            <tr>
              <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_SUITABILITY') ?></td>
              <td><?php echo implode(', ', $this->unit_facilities['Suitability']) ?></td>         
            </tr>
          <?php endif; ?>

          <!-- Linen costs -->
          <?php if ($this->item->linen_costs) : ?>
            <tr>
              <td><?php echo JText::_('COM_ACCOMMODATION_SITE_LINEN_COSTS') ?></td>
              <td><?php echo $this->item->linen_costs; ?></td>        
            </tr>
          <?php endif; ?>       
          <?php if (array_key_exists('Kitchen features', $this->unit_facilities)) : ?>
            <tr>
              <td><?php echo JText::_('COM_ACCOMMODATION_SITE_KITCHEN_FEATURES') ?></td>
              <td><?php echo implode(', ', $this->unit_facilities['Kitchen features']) ?></td>         
            </tr>      
          <?php endif; ?>   
          <?php if (array_key_exists('Property Facilities', $this->unit_facilities)) : ?>
            <tr>
              <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_INTERNAL') ?></td>
              <td><?php echo implode(', ', $this->unit_facilities['Property Facilities']) ?></td>         
            </tr>      
          <?php endif; ?>    

          <?php if (array_key_exists('External Facilities', $this->unit_facilities)) : ?>
            <tr>
              <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_EXTERNAL') ?></td>
              <td><?php echo implode(', ', $this->unit_facilities['External Facilities']) ?></td>         
            </tr>      
          <?php endif; ?>   
          <?php if (array_key_exists('Activities nearby', $this->property_facilities)) : ?>
            <tr>
              <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_ACTIVITIES_NEARBY') ?></td>
              <td><?php echo implode(', ', $this->property_facilities['Activities nearby']) ?></td>         
            </tr>      
          <?php endif; ?> 
        </tbody>
      </table>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5">
    </div>
  </div>

  <?php if ($this->item->unit_title) : ?>
    <h2 class="page-header" ><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_AVAILABILITY_AT', $this->item->unit_title)) ?></h2> 
  <?php endif; ?> 
  <div class="row" id="availability">
    <div class="col-lg-7 col-md-7 col-sm-7">

      <?php if ($this->item->changeover_day) : ?>
        <p>
          <strong>
            <?php echo JText::_('COM_ACCOMMODATION_CHANGEOVER_DAY') ?>
          </strong>
          <?php echo htmlspecialchars($this->item->changeover_day) ?>
        </p>
      <?php endif; ?>   
      <p>
        <?php echo JText::sprintf('COM_ACCOMMODATION_AVAILABILITY_LAST_UPDATED_ON', $this->item->availability_last_updated_on) ?>
      </p>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5">
      <table class="table table-condensed availability-key">
        <thead> 
          <tr>
            <th class="available">&nbsp;</th>
            <th><?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_KEY_AVAILABLE') ?></th>

            <th class="unavailable">&nbsp;</th>
            <th><?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_KEY_UNAVAILABLE') ?></th>
          </tr>
        </thead>
      </table>    
    </div>
  </div>
  <div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
      <hr />
      <div class="sidescroll-nextprev">
        <div class="overthrow sidescroll">
          <?php if ($this->availability) : ?>
            <?php echo $this->availability; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row" id="tariffs">
    <div class="col-lg-7 col-md-7 col-sm-7">
      <?php if ($this->item->unit_title) : ?>
        <h2 class="page-header"><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_TARIFFS_AT', $this->item->unit_title)) ?></h2> 
      <?php endif; ?>    
      <?php if ($this->tariffs) : ?>
        <?php echo $this->loadTemplate('tariffs'); ?>
      <?php else: ?>
        <p>No tariffs were found for this property. Please enquire with the owner for rental rates for this property</p>
      <?php endif; ?>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5">  
      <?php if ($this->item->additional_price_notes) : ?>
        <h2><?php echo JText::_('COM_ACCOMMODATION_ADDITIONAL_PRICE_NOTES') ?></h2>
        <?php echo $this->item->additional_price_notes ?>
      <?php endif; ?>
    </div>  
  </div>
  <div class="row" id="email">
    <div class="col-lg-12">
      <?php if ($this->item->unit_title) : ?>
        <h2 class="page-header"><?php echo ($this->item->is_bookable) ? JText::_('COM_ACCOMMODATION_BOOK_THIS_PROPERTY') : JText::_('COM_ACCOMMODATION_EMAIL_THE_OWNER') ?></h2> 
      <?php endif; ?>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-7 col-md-7 col-sm-7">
      <?php echo $this->loadTemplate('form'); ?>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5">
      <?php if ($this->item->is_bookable) : ?>
        <?php echo $this->loadTemplate($owner); ?>

      <?php else: ?>
        <?php echo $this->loadTemplate('contact_owner'); ?>
      <?php endif; ?>

    </div>
  </div>
</div>


