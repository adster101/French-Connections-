<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$language = JFactory::getLanguage();
$lang = $language->getTag();
$app = JFactory::getApplication();

$Itemid = SearchHelper::getItemid(array('component', 'com_accommodation'));
$HolidayMakerLogin = SearchHelper::getItemid(array('component', 'com_users'));
$searchID = SearchHelper::getItemid(array('component', 'com_fcsearch'));

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
$search_route = 'index.php?option=com_fcsearch&Itemid=' . (int) $searchID . '&s_kwds=france';

// TO DO - Should also add a
$owner = JFactory::getUser($this->item->created_by)->username;

if ((int) $preview && $preview == 1) {
    $link .= '&preview=1';
}

$route = JRoute::_($link);

if ((int) $preview && $preview == 1) {
    $append = '&preview=1';
}

$price_range = array();
if (!empty($this->tariffs)) {
    foreach ($this->tariffs as $tariff) {
        $price_range[] = $tariff->tariff;
    }
}
$langs_array = array();

if (!empty($this->item->languages_spoken)) {
    $langs = json_decode($this->item->languages_spoken);

    foreach ($langs as $lang) {
        if (!empty($lang)) {
            $langs_array[] = JText::_($lang);
        }
    }
}

$amenities = ($this->item->local_amenities) ? json_decode($this->item->local_amenities) : array();

$accordion_navigator = new JLayoutFile('frenchconnections.property.accordion');
$accordion_data = new StdClass;

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
$search_url = $app->getUserState('user.search');
$crumbs = JModuleHelper::getModules('breadcrumbs'); //If you want to use a different position for the modules, change the name here in your override.  
$mpu = JModuleHelper::getModules('property-mpu'); //If you want to use a different position for the modules, change the name here in your override.  
?>

<div id="top" class="container hidden-xs">
    <h1 class="page-header">
        <?php echo $this->document->title; ?>
    </h1>
    <!-- Begin breadcrumbs -->
    <?php foreach ($crumbs as $module) : // Render the cross-sell modules etc    ?>
        <?php echo JModuleHelper::renderModule($module, array('style' => 'no', 'id' => 'section-box')); ?>
    <?php endforeach; ?>
    <!-- End breadcrumbs -->
    <?php if (count($this->units) > 1) : ?>
        <?php echo $this->loadTemplate('units'); ?>
    <?php endif; ?>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="property-buttons-row clearfix">
                <?php if (!empty($search_url)) : ?>
                    <a class="btn btn-primary btn-sm" href="<?php echo $search_url ?>" title="">    
                        <span class="glyphicon glyphicon-circle-arrow-left"></span>
                        <?php echo JText::_('COM_ACCOMMODATION_BACK_TO_SEARCH_RESULTS'); ?>
                    </a>
                <?php else: ?>
                    <a class="btn btn-primary btn-sm" href="<?php echo $search_route ?>" title="">    
                        <span class="glyphicon glyphicon-circle-arrow-left"></span>
                        <?php echo JText::_('COM_ACCOMMODATION_BROWSE_SEARCH_RESULTS'); ?>
                    </a>          
                <?php endif; ?>
                <div class="pull-right">
                    <?php if ($logged_in) : ?>
                        <?php echo $shortlist->render($displayData); ?>
                    <?php else : ?>
                        <a class="btn btn-default btn-sm" href="<?php echo JRoute::_('index.php?option=com_users&Itemid=' . (int) $HolidayMakerLogin) ?>">
                            <span class="glyphicon glyphicon-heart muted"></span>
                            <span class=""><?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?></span>
                        </a>
                    <?php endif; ?>
                    <button class='btn btn-default hidden-xs btn-sm' type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="glyphicon glyphicon-share-alt"></span>
                        <?php echo JText::_('COM_ACCOMMODATION_SHARE') ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <li> 
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
                            ?>">
                                <span class="glyphicon social-icon facebook"></span>  
                                <?php echo JText::_('COM_ACCOMMODATION_FACEBOOK') ?>
                            </a>
                        </li> 
                        <li>
                            <a target="_blank" href="<?php echo 'http://twitter.com/share?url=' . $uri . '&amp;text=' . $this->escape($this->item->unit_title) ?>" >
                                <span class="glyphicon social-icon twitter"></span>
                                <?php echo JText::_('COM_ACCOMMODATION_TWITTER') ?>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="<?php echo 'https://plus.google.com/share?url=' . $uri ?>">
                                <span class="glyphicon social-icon google-plus"></span>
                                <?php echo JText::_('COM_ACCOMMODATION_GOOGLE_PLUS') ?>
                            </a>
                        </li>
                    </ul>
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
        <div class="col-lg-4 col-lg-push-8 col-md-4 col-md-push-8 col-sm-5 col-sm-push-7 col-xs-12 key-facts">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php if ($this->tariffs) : ?> 
                        <?php if (min($price_range) == max($price_range)) : ?>
                            <p>
                                <span class="lead">
                                    <strong>&pound;<?php echo $min_prices['GBP'] ?></strong>
                                </span>
                                (&euro;<?php echo $min_prices['EUR']; ?>)
                                <br />
                                <?php if ($this->item->tariffs_based_on) : ?>
                                    <span class="small">
                                        <?php echo htmlspecialchars($this->item->tariffs_based_on); ?>
                                    </span>
                                <?php endif; ?>
                            </p>             
                        <?php else: ?>
                            <p>
                                <span class="lead"><strong>&pound;<?php echo $min_prices['GBP'] . ' - &pound;' . $max_prices['GBP']; ?></strong></span>
                                (&euro;<?php echo $min_prices['EUR'] . ' - &euro;' . $max_prices['EUR']; ?>)
                                <br />
                                <?php if ($this->item->tariffs_based_on) : ?>
                                    <span class="small">
                                        <?php echo htmlspecialchars($this->item->tariffs_based_on); ?>
                                    </span>
                                <?php endif; ?>
                            </p> 
                        <?php endif; ?>
                    <?php else: ?>
                        <?php echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST'); ?>
                    <?php endif; ?>
                    <!-- Max capacity/occupancy -->
                    <?php if ($this->item->occupancy) : ?>
                        <p>
                            <mark>
                                <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_OCCUPANCY_BEDS_BATHROOMS', $this->item->property_type, $this->item->occupancy, $this->item->bedrooms, $this->item->bathrooms); ?>
                            </mark>
                        </p>
                    <?php endif; ?>
                    <div class="visible-xs">
                        <p>
                            <a class="btn btn-danger btn-block" id="enquiry" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . $append); ?>#email">
                                <?php echo ($this->item->is_bookable) ? JText::_('COM_ACCOMMODATION_SITE_BOOK_NOW') : JText::_('COM_ACCOMMODATION_SITE_CONTACT_OWNER'); ?>  
                            </a>
                        </p>
                        <p>
                            <a class="btn btn-warning btn-block" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id . $append); ?>#availability">
                                <?php echo JText::_('COM_ACCOMMODATION_SITE_CHECK_AVAILABILITY'); ?>  
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="hidden-xs">
                <?php echo $this->loadTemplate('top_form'); ?>
                <?php if ($this->item->is_bookable) : ?>
                    <?php echo $this->loadTemplate($owner); ?>
                <?php else: ?>
                    <?php echo $this->loadTemplate('contact_owner'); ?>
                <?php endif; ?>
            </div>
            <div class="text-center hidden-xs">
                <?php foreach ($mpu as $item) : // Render the cross-sell modules etc   ?>
                    <?php echo JModuleHelper::renderModule($item, array('style' => 'no', 'id' => 'section-box')); ?>
                <?php endforeach; ?> 
            </div>
        </div> 
        <div class="col-lg-8 col-lg-pull-4 col-md-8 col-md-pull-4 col-sm-pull-5 col-sm-7 col-xs-12">
            <!-- Start gallery -->
            <?php echo $this->loadTemplate('gallery'); ?>
            <!-- End gallery -->

            <div class="well well-sm well-light-blue hidden-xs">
                <h5>Key facts</h5>
                <!-- External facilities inc. pool type-->
                <?php if (array_key_exists('External Facilities', $this->unit_facilities) || (array_key_exists('Suitability', $this->unit_facilities))) : ?>
                    <p class="">
                        <?php if (array_key_exists('External Facilities', $this->unit_facilities)) : ?> 
                            <strong>
                                <?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_EXTERNAL'); ?>
                            </strong>
                            <?php echo implode(', ', $this->unit_facilities['External Facilities']) ?>
                        <?php endif; ?>  
                    </p>
                    <p>
                        <?php if (array_key_exists('Suitability', $this->unit_facilities)) : ?>
                            <strong>
                                <?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_SUITABILITY'); ?>
                            </strong>
                            <?php echo implode(', ', $this->unit_facilities['Suitability']) ?>
                        <?php endif; ?>      
                    </p>   
                <?php endif; ?>
                <!-- LWL Bullshine -->
                <?php if ($this->item->lwl) : ?>
                    <p class="">
                        <?php echo Jtext::_('COM_ACCOMMODATION_THIS_PROPERTY_OFFERS_LONG_WINTER_LETS'); ?>
                    </p>
                <?php endif; ?>
            </div> 

            <div id="property-accordion" class="panel-group">
                <div id="about" class="panel panel-default">
                    <?php if ($this->item->unit_title) : ?>
                        <div class="panel-heading">
                            <?php
                            $accordion_data->title = $this->escape($this->item->unit_title);
                            $accordion_data->target = 'description-panel';
                            $accordion_data->glyph = 'home';
                            echo $accordion_navigator->render($accordion_data);
                            ?>     
                        </div>
                    <?php endif; ?>
                    <?php if ($this->item->description) : ?>
                        <div class="panel-collpase collapse in" id="description-panel"> 
                            <div class="panel-body">             
                                <?php echo $this->item->description; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($this->item->location_details)) : ?>
                    <div id="location" class="panel panel-default">
                        <?php if ($this->item->unit_title) : ?>
                            <div class="panel-heading">

                                <?php
                                $accordion_data->title = $this->escape(JText::sprintf('COM_ACCOMMODATION_ABOUT_ACCOMMODATION_IN_FOUR', $this->item->city));
                                $accordion_data->target = 'location-panel';
                                $accordion_data->glyph = 'map-marker';
                                echo $accordion_navigator->render($accordion_data);
                                ?>            
                            </div>             
                        <?php endif; ?>
                        <div class="panel-collpase collapse in" id="location-panel"> 
                            <div class="panel-body">
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
                                <h4 class="page-header">
                                    <?php echo JText::sprintf('COM_ACCOMMODATION_ABOUT_ON_THE_MAP_IN', $this->item->city) ?>
                                </h4>  
                                <div id="property_map_canvas" style="width:100%; height:370px;margin-bottom: 9px;" class="clearfix" data-hash="<?php echo JSession::getFormToken() ?>" data-lat="<?php echo $this->escape($this->item->latitude) ?>" data-lon="<?php echo $this->escape($this->item->longitude) ?>"></div>
                                <p class="key text-right">
                                    <span>
                                        <img src="/images/mapicons/iconflower.png" />&nbsp;<?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_MARKER_KEY', $this->item->property_id) ?>
                                        &nbsp;&ndash;&nbsp;
                                        <img src="/images/mapicons/iconplaceofinterest.png" />&nbsp;<?php echo JText::_('COM_ACCOMMODATION_PLACEOFINTEREST_MARKER_KEY') ?>
                                    </span>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($this->item->getting_there)) : ?>
                                <?php if ($this->item->unit_title) : ?>
                                    <h4 class="page-header" >
                                        <?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_HOW_TO_GET_TO_ACCOMMODATION_IN', $this->item->unit_title)) ?>
                                    </h4>  
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
                            <?php endif; ?>   
                        </div>
                    </div>
                </div>
                <!-- Begin reviews -->

                <?php echo $this->loadTemplate('reviews'); ?>
                <!-- End reviews -->

                <div id="facilities" class="panel panel-default">
                    <?php if ($this->item->unit_title) : ?>
                        <div class="panel-heading">
                            <?php
                            $accordion_data->title = $this->escape(JText::sprintf('COM_ACCOMMODATION_FACILITIES_AT_FOUR'));
                            $accordion_data->target = 'facilities-panel';
                            $accordion_data->glyph = 'th-list';
                            echo $accordion_navigator->render($accordion_data);
                            ?>
                        </div>         
                    <?php endif; ?>
                    <div class="panel-collpase collapse in" id="facilities-panel"> 
                        <div class="panel-body">
                            <table class="table table-striped table-responsive">
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
                                    if ($this->item->bedrooms && $this->item->bedrooms > 0) {
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

                                    <!-- Distance to coast -->
                                    <?php if ($this->item->distance_to_coast) : ?>
                                        <tr class="dotted">
                                            <td>
                                                <?php echo JText::_('COM_ACCOMMODATION_SITE_DISTANCE_TO_COAST'); ?></td>
                                            <td><?php echo $this->item->distance_to_coast; ?></td>
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
                    </div>
                </div>
                <div id="availability" class="panel panel-default">
                    <?php if ($this->item->unit_title) : ?>
                        <div class="panel-heading">
                            <?php
                            $accordion_data->title = $this->escape(htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_AVAILABILITY_AT_FOUR')));
                            $accordion_data->target = 'availability-panel';
                            $accordion_data->glyph = 'calendar';
                            echo $accordion_navigator->render($accordion_data);
                            ?>
                        </div>        
                    <?php endif; ?> 
                    <div class="panel-collpase collapse in" id="availability-panel"> 
                        <div class="panel-body">
                            <div class="row" >
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
                        </div>
                    </div>
                </div>

                <div class="panel panel-default" id="tariffs">
                    <?php if ($this->item->unit_title) : ?>
                        <div class="panel-heading">
                            <?php
                            $accordion_data->title = $this->escape(htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_TARIFFS_AT_FOUR')));
                            $accordion_data->target = 'tariffs-panel';
                            $accordion_data->glyph = 'euro';
                            echo $accordion_navigator->render($accordion_data);
                            ?>
                        </div>            
                    <?php endif; ?>  
                    <div class="panel-collpase collapse in" id="tariffs-panel"> 
                        <div class="panel-body">
                            <?php if ($this->tariffs) : ?>
                                <?php echo $this->loadTemplate('tariffs'); ?>
                            <?php else: ?>
                                <p>No tariffs were found for this property. Please enquire with the owner for rental rates for this property</p>
                            <?php endif; ?>

                            <?php if ($this->item->additional_price_notes) : ?>
                                <h3><?php echo JText::_('COM_ACCOMMODATION_ADDITIONAL_PRICE_NOTES') ?></h3>
                                <?php echo $this->item->additional_price_notes ?>
                            <?php endif; ?>
                        </div>  
                    </div>
                </div>
            </div>
            <!-- Begin page navigator -->
            <?php echo $this->loadTemplate('navigator'); ?>
            <!-- End page navigator -->
            <div class="hidden-md hidden-sm hidden-lg">
                <div id="email">
                    <?php if ($this->item->unit_title) : ?>
                        <h2 class="page-header"><?php echo ($this->item->is_bookable) ? JText::_('COM_ACCOMMODATION_BOOK_THIS_PROPERTY') : JText::_('COM_ACCOMMODATION_EMAIL_THE_OWNER') ?></h2> 
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-lg-7 col-md-7 col-sm-7">
                        <?php echo $this->loadTemplate('top_form'); ?>
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
        </div>
    </div>
</div>
