<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$language = JFactory::getLanguage();
$lang = $language->getTag();
$app = JFactory::getApplication();

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

// The layout for the anchor based navigation on the property listing
$navigator = new JLayoutFile('navigator', $basePath = JPATH_SITE . '/components/com_accommodation/layouts');

// Add the reviews to item for the above layout.
// TO DO - refactor so that $this->item contains all elements of the listing for use in layouts?
$this->item->reviews = $this->reviews;

JHTML::_('behavior.formvalidation');

// Include the content helper so we can get the route of the success article
require_once JPATH_SITE . '/components/com_content/helpers/route.php';

// Register the general helper class
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');

$min_prices = (!empty($this->tariffs)) ? JHtmlGeneral::price(min($price_range), $this->item->base_currency, $this->item->exchange_rate_eur, $this->item->exchange_rate_usd) : '';
$max_prices = (!empty($this->tariffs)) ? JHtmlGeneral::price(max($price_range), $this->item->base_currency, $this->item->exchange_rate_eur, $this->item->exchange_rate_usd) : '';
?>


<div class="page-header"> 
  <?php echo $this->loadTemplate('social'); ?>

  <h1>
    <?php echo $this->document->title; ?>
  </h1>

</div>

<?php if (count($this->offer)) : ?>
  <div class="well well-small">
    <h5>   
      <?php echo $this->escape($this->offer->title); ?>
    </h5>
    <p>
      <?php echo $this->escape($this->offer->description); ?>  
    </p>
  </div>

<?php endif; ?>
<?php if (count($this->units) > 1) : ?>
  <?php echo $this->loadTemplate('units'); ?>
<?php endif; ?>
<div class="row-fluid">
  <div class="span7">
    <!-- Image gallery -->
    <!-- Needs go into a separate template -->
    <div id="main" role="main">
      <?php if (count($this->images) > 1) : ?>

        <section class="slider">
          <div id="slider" class="flexslider">
            <ul class="slides">
              <?php foreach ($this->images as $images => $image) : ?> 
                <li>
                  <img src="<?php echo JURI::root() . 'images/property/' . $this->item->unit_id . '/gallery/' . $image->image_file_name; ?>" />
                  <p class="flex-caption">
                    <?php echo $image->caption; ?>
                  </p>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div id="carousel" class="flexslider">
            <ul class="slides">
              <?php foreach ($this->images as $images => $image) : ?> 
                <li>
                  <img src="<?php echo JURI::root() . 'images/property/' . $this->item->unit_id . '/thumbs/' . $image->image_file_name ?>" /> 
                </li>     
              <?php endforeach; ?>
            </ul>
          </div>
        </section>
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
  <div class="span5 key-facts">
    <div class="well">

      <?php if ($this->tariffs) : ?> 
        <?php if (min($price_range) == max($price_range)) : ?>
          <p>
            <span class="lead large"><strong>&pound;<?php echo $min_prices['GBP'] ?></strong></span> 
            <span class="muted" style="text-align: right">(<i>Approx:</i> &euro;<?php echo $min_prices['EUR']; ?>)</span>
            <br />
            <?php if ($this->item->tariffs_based_on) : ?>
              <?php echo htmlspecialchars($this->item->tariffs_based_on); ?>
            <?php endif; ?>
          </p>             

        <?php else: ?>
          <span class="lead large">
            <strong>&pound;<?php echo $min_prices['GBP'] . ' - &pound;' . $max_prices['GBP']; ?></strong> 
          </span>    
          <?php if ($this->item->tariffs_based_on) : ?>
            <?php echo '&nbsp;' . htmlspecialchars($this->item->tariffs_based_on); ?>
          <?php endif; ?>
          <br /><span class="muted">(<i>Approx:</i> &euro;<?php echo $min_prices['EUR'] . ' - &euro;' . $max_prices['EUR']; ?>)</span>
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
          <?php echo JText::_('COM_ACCOMMODATION_SITE_CHANGEOVER_DAY'); ?>
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
        <p class="dotted clearfix">
          <?php if (array_key_exists('External Facilities', $this->unit_facilities)) : ?> 
            <span>
              <strong><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_EXTERNAL'); ?></strong>
              <?php echo implode(', ', $this->unit_facilities['External Facilities']) ?>
            </span><br /><br />
          <?php endif; ?>  
          <?php if (array_key_exists('Suitability', $this->unit_facilities)) : ?>
            <span>
              <strong><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_SUITABILITY'); ?></strong>
              <?php echo implode(', ', $this->unit_facilities['Suitability']) ?>
            </span>         
          <?php endif; ?>      
        </p>   
      <?php endif; ?>

      <hr class="clear" />

      <?php if ($this->reviews) : ?>
        <figure>
          <blockquote class="quote">
            <?php echo strip_tags(JHtml::_('string.truncate', $this->reviews[0]->review_text, 100)); ?>

            <p>
              <a href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id) ?>#reviews">
                <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_READ_MORE_REVIEWS', count($this->reviews)); ?>
              </a>
            </p>
          </blockquote> 
          <figcaption>
            <cite>

              <?php echo $this->reviews[0]->guest_name; ?>
              <?php
              $date = new DateTime($this->reviews[0]->date);
              echo $date->format('D, d M Y');
              ?>
            </cite> 
          </figcaption>
        </figure>
      <?php else: ?>
        <p>
          <?php echo JText::_('COM_ACCOMMODATION_SITE_NO_REVIEWS'); ?>
        </p>
      <?php endif; ?>
      <p>
        <a href="<?php echo JRoute::_('index.php?option=com_reviews&view=reviews&Itemid=194&id=' . $this->item->property_id); ?>">
          <?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_REVIEW'); ?>
        </a>
      </p>

      <hr />
      <p class="center">
        <a class="btn btn-large" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id) ?>#availability">
          <?php echo JText::_('COM_ACCOMMODATION_SITE_CHECK_AVAILABILITY'); ?>  
        </a>
        <a class="btn btn-primary btn-large" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id); ?>#email">
          <?php echo JText::_('COM_ACCOMMODATION_SITE_CONTACT_OWNER'); ?>  
        </a>
      </p>
    </div>  
  </div> 
</div>



<div class="row-fluid" id="about">
  <?php $this->item->navigator = 'about'; ?>
  <?php echo $navigator->render($this->item); ?>
</div>
<div class="row-fluid">
  <div class="span8" >
    <?php if ($this->item->unit_title) : ?>
      <h2><?php echo $this->escape($this->item->unit_title) ?>
        <?php //echo JText::sprintf('HOLIDAY_ACCOMMODATION_AT', $this->item->accommodation_type, $this->item->unit_title) ?></h2>  
    <?php endif; ?>
    <?php if ($this->item->description) : ?>
      <?php echo $this->item->description; ?>
    <?php endif; ?>
  </div>
  <div class="span4">
    <?php
    jimport('joomla.application.module.helper');
    $modules = JModuleHelper::getModule('mod_OpenX_spc', 'MPU-LISTING');
    $attribs['style'] = 'html5';
    echo JModuleHelper::renderModule($modules, $attribs);
    ?>
  </div>
</div>

<div class="row-fluid" id="location">
  <?php $this->item->navigator = 'location'; ?>
  <?php echo $navigator->render($this->item); ?>
</div>
<div class="row-fluid">
  <div class="span8">
    <?php if ($this->item->unit_title) : ?>
      <h2><?php echo JText::sprintf('COM_ACCOMMODATION_ABOUT_ACCOMMODATION_IN', $this->item->city, $this->item->department, $this->item->region) ?></h2>  
    <?php endif; ?>
    <?php if ($this->item->location_details) : ?>
      <?php echo $this->item->location_details; ?>
    <?php endif; ?> 
    <h3><?php echo JText::sprintf('COM_ACCOMMODATION_ABOUT_ON_THE_MAP', $this->item->city, $this->item->department, $this->item->region) ?></h3>  
    <div id="map_canvas" style="width:100%; height:370px;margin-bottom: 9px;" class="clearfix" data-lat="<?php echo $this->escape($this->item->latitude) ?>" data-lon="<?php echo $this->escape($this->item->longitude) ?>"></div>
  </div>
  <div class="span4">

  </div>
</div>


<div class="row-fluid" id="gettingthere">
  <?php $this->item->navigator = 'gettingthere'; ?>
  <?php echo $navigator->render($this->item); ?>
</div>
<div class="row-fluid">
  <div class="span8">
    <?php if ($this->item->unit_title) : ?>
      <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_HOW_TO_GET_TO_ACCOMMODATION_IN', $this->item->unit_title)) ?></h2>  
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
  <div class="span4"> 

  </div>
</div>

<?php if ($this->reviews) : ?>

  <div class="row-fluid" id="reviews">
    <?php $this->item->navigator = 'reviews'; ?>
    <?php echo $navigator->render($this->item); ?>
  </div>
  <div class="row-fluid">
    <div class="span8">
      <?php if ($this->item->unit_title) : ?>
        <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_REVIEWS_AT', $this->item->unit_title)) ?></h2> 
      <?php endif; ?>
      <div class="well well-small">
        <?php foreach ($this->reviews as $review) : ?>
          <figure>
            <blockquote class="quote">
              <?php echo strip_tags($review->review_text, '<p>,<br>'); ?> 
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
    <div class="span4"></div>
  </div>
<?php endif; ?>

<div class="row-fluid" id="facilities">
  <?php $this->item->navigator = 'facilities'; ?>
  <?php echo $navigator->render($this->item); ?>
</div>
<div class="row-fluid">
  <div class="span8">
    <?php if ($this->item->unit_title) : ?>
      <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_FACILITIES_AT', $this->item->unit_title)) ?></h2> 
    <?php endif; ?>
    <table class="table table-striped">
      <?php if (array_key_exists('Property Type', $this->unit_facilities) && array_key_exists('Accommodation Type', $this->unit_facilities)) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_PROPERTY_TYPE') ?></td>
          <td><?php echo $this->unit_facilities['Property Type'][0] . ' (' . $this->unit_facilities['Accommodation Type'][0] . ')'; ?></td>        
        </tr>
      <?php endif; ?>

      <?php if ($this->item->bathrooms) : ?>    
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_BATHROOMS') ?></td>
          <td><?php echo $this->item->bathrooms; ?></td>        
        </tr>
      <?php endif; ?>

      <!-- Bedrooms -->
      <?php if ($this->item->bedrooms && $this->item->bedrooms > 0) { ?>    
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

      <?php if (array_key_exists('Property Facilities', $this->unit_facilities)) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_INTERNAL') ?></td>
          <td><?php echo implode(', ', $this->unit_facilities['Property Facilities']) ?></td>         
        </tr>      
      <?php endif; ?>   
      <?php if (array_key_exists('Activities nearby', $this->property_facilities)) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_ACTIVITIES_NEARBY') ?></td>
          <td><?php echo implode(', ', $this->property_facilities['Activities nearby']) ?></td>         
        </tr>      
      <?php endif; ?>  

      <?php if (array_key_exists('External Facilities', $this->unit_facilities)) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_EXTERNAL') ?></td>
          <td><?php echo implode(', ', $this->unit_facilities['External Facilities']) ?></td>         
        </tr>      
      <?php endif; ?>   

      <?php if (array_key_exists('Kitchen features', $this->unit_facilities)) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_KITCHEN_FEATURES') ?></td>
          <td><?php echo implode(', ', $this->unit_facilities['Kitchen features']) ?></td>         
        </tr>      
      <?php endif; ?>       
    </table>
  </div>
  <div class="span4">
  </div>
</div>





<div class="row-fluid" id="availability">  
  <?php $this->item->navigator = 'availability'; ?>
  <?php echo $navigator->render($this->item); ?>
</div>
<div clas="row-fluid">
  <div class="span8">
    <?php if ($this->item->unit_title) : ?>
      <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_AVAILABILITY_AT', $this->item->unit_title)) ?></h2> 
    <?php endif; ?>
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
  <div class="span4">
    <table class="key">
      <tr>
        <td class="available"></td>
        <td>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_KEY_AVAILABLE') ?></td>

        <td class="unavailable">&nbsp;</td>
        <td>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_KEY_UNAVAILABLE') ?></td>
      </tr>
    </table>    
  </div>
</div>
<div class="row-fluid">
  <div class="span12">
    <?php if ($this->availability) : ?>
      <?php echo $this->availability; ?>
    <?php endif; ?>
  </div>
</div>



<div class="row-fluid" id="tariffs">
  <?php $this->item->navigator = 'tariffs'; ?>
  <?php echo $navigator->render($this->item); ?>
</div>
<div class="row-fluid">
  <div class="span8">
    <?php if ($this->item->unit_title) : ?>
      <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_TARIFFS_AT', $this->item->unit_title)) ?></h2> 
    <?php endif; ?>    
    <?php if ($this->tariffs) : ?>
      <?php echo $this->loadTemplate('tariffs'); ?>
    <?php else: ?>
      <p>No tariffs were found for this property. Please enquire with the owner for rental rates for this property</p>
    <?php endif; ?>
  </div>
  <div class="span4">
    <h3><?php echo JText::_('COM_ACCOMMODATION_ADDITIONAL_PRICE_NOTES') ?></h3>
    <?php if ($this->item->additional_price_notes) : ?>
      <?php echo $this->item->additional_price_notes ?>
    <?php else: ?>
      <?php echo JText::_('COM_ACCOMMODATION_ADDITIONAL_PRICE_NOTES_NONE') ?>
    <?php endif; ?>
  </div>
</div>

<div class="row-fluid" id="email">
  <?php $this->item->navigator = 'email'; ?>
  <?php echo $navigator->render($this->item); ?>
</div>
<div class="row-fluid">
  <div class="span12">
    <?php if ($this->item->unit_title) : ?>
      <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_EMAIL_THE_OWNER', $this->item->unit_title)) ?></h2> 
    <?php endif; ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span8">
    <?php echo $this->loadTemplate('form'); ?>
  </div>
  <div class="span4">
    <h3><?php echo htmlspecialchars(JText::_('COM_ACCOMMODATION_CONTACT_THE_OWNER')); ?></h3> 
    <p>
      <?php echo $this->item->name; ?><br />
      <span class="small">(<?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_ADVERTISING_SINCE', $this->item->advertising_since)); ?>)</span>
    </p>

    <p>
      <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL'); ?>
      <?php echo $this->item->phone_1; ?>
    </p>

    <?php if ($this->item->phone_2) : ?>
      <p>
        <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL2'); ?>
        <?php echo $this->item->phone_2; ?>
      </p>
    <?php endif; ?>
    <?php if ($this->item->phone_3) : ?>
      <p>
        <?php echo JText::_('COM_ACCOMMODATION_CONTACT_TEL3'); ?>
        <?php echo $this->item->phone_3; ?>
      </p>
    <?php endif; ?>  
    <?php if (count($langs_array) > 0) : ?>
      <p><?php echo JText::sprintf('COM_ACCOMMODATION_LANGUAGES_SPOKEN', implode(', ', $langs_array)); ?></p>
    <?php endif; ?>
    <?php if ($this->item->booking_form) : ?>
      <?php $link = JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id) . '&tmpl=component&view=bookingform'; ?>
      <p><?php echo JText::sprintf('COM_ACCOMMODATION_BOOKING_FORM_VIEW', $link); ?></p>
    <?php endif; ?>

    <?php if ($this->item->website) : ?>
      <p>
        <?php echo JText::_('COM_ACCOMMODATION_CONTACT_WEBSITE'); ?>
        <a target="_blank" rel="nofollow" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id) . '&' . JSession::getFormToken() . '=1&task=listing.viewsite'; ?>">
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



