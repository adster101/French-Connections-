<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$language = JFactory::getLanguage();
$lang = $language->getTag();
$price_range = array();
foreach ($this->tariffs as $tariff) {
  $price_range[] = $tariff->tariff;
}

JHtml::_('behavior.keepalive');
JHTML::_('behavior.formvalidation');
?>

<div class="row-fluid">
  <div class="span12">
    <?php echo $this->loadTemplate('crumbs'); ?>
  </div>  
</div>
<div class="page-header">
  <h1>
    <?php echo $this->document->title; ?>
  </h1>
</div>
<?php if (count($this->units) > 1) : ?>
  <?php echo $this->loadTemplate('units'); ?>
<?php endif; ?>
<div class="row-fluid">
  <div class="span5 key-facts">
    <div class="well">
      <div class="clearfix">	
        <p class="pull-left">
          <a class="btn btn-small" href="#">
            <i class="icon-bookmark"> </i><?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_TO_FAVOURITES') ?>
          </a>
        </p>
        <p class="pull-right addthis_default_style">
          <!-- AddThis Button BEGIN -->
          <a class="addthis_button_print " title="Print" href="#"></a>
          <a class="addthis_button_facebook " title="Send to Facebook" href="#"></a>
          <a class="addthis_button_twitter " title="Tweet This" href="#"></a>
          <a class="addthis_button_email " title="Email" href="#"></a>
          <a class="addthis_button_compact" href="#"></a>          
          <!-- AddThis Button END -->	
        </p>
      </div>
      <p>
        <span class="lead large">
          <?php if ($this->tariffs) { ?>
            <strong>
              <?php if (min($price_range) == max($price_range)) { 
                  echo htmlspecialchars($this->item->base_currency) . min($price_range);
                        
                } else {
                  echo htmlspecialchars($this->item->base_currency) . min($price_range) . ' - ' . htmlspecialchars($this->item->base_currency) . max($price_range); 
                } ?>
            </strong> 
          </span>
          <?php if ($this->item->tariffs_based_on) : ?>
            <?php echo '&nbsp;' . htmlspecialchars($this->item->tariffs_based_on); ?>
          <?php endif; ?>
        <?php } else { ?>
          <?php echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST'); ?>
        <?php } ?>
      </p>
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
      <!-- Location type -->
      <?php if ($this->item->location_type) : ?>
        <p class="dotted">
          <?php echo JText::_('COM_ACCOMMODATION_SITE_LOCATION_TYPE'); ?>
          <span class="pull-right"><?php echo $this->item->location_type; ?></span>
        </p>
      <?php endif; ?>
      <!-- Location type -->
      <?php if ($this->item->swimming) : ?>
        <p class="dotted">
          <?php echo JText::_('COM_ACCOMMODATION_SITE_SWIMMING_FACILITIES'); ?>
          <span class="pull-right"><?php echo $this->item->swimming; ?></span>
        </p>
      <?php endif; ?>
      <!-- Changeover day -->
      <?php if ($this->item->changeover_day) : ?>
        <p class="dotted">
          <?php echo JText::_('COM_ACCOMMODATION_SITE_CHANGEOVER_DAY'); ?>
          <span class="pull-right"><?php echo $this->item->changeover_day; ?></span>
        </p>
      <?php endif; ?>
      <!-- Changeover day -->
      <?php if ($this->item->property_type) : ?>
        <p class="dotted">
          <?php echo JText::_('COM_ACCOMMODATION_SITE_PROPERTY_TYPE'); ?>
          <span class="pull-right"><?php echo $this->item->property_type; ?></span>
        </p>
      <?php endif; ?>

      <hr />



      <?php if ($this->reviews) : ?>
        <blockquote>
          <p>
            <?php echo strip_tags(JHtml::_('string.truncate', $this->reviews[0]->review_text, 125)); ?>

          </p>
          <small>
            <?php echo $this->reviews[0]->guest_name; ?>
            <cite title="Date Stayed">
              <?php
              $date = new DateTime($this->reviews[0]->date);

              echo $date->format('D, d M Y');
              ?>
            </cite>  
            <a href="#reviews">
              <?php echo JText::_('COM_ACCOMMODATION_SITE_READ_MORE'); ?>
            </a>

          </small>
        </blockquote>   

      <?php else: ?>
        <p>
          <?php echo JText::_('COM_ACCOMMODATION_SITE_NO_REVIEWS'); ?>
        </p>
      <?php endif; ?>
      <p>
        <a href="<?php echo JRoute::_('http://dev.frenchconnections.co.uk/index.php?option=com_reviews&view=reviews&Itemid=167&id=' . $this->item->id); ?>">
          <?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_REVIEW'); ?>
        </a>
      </p>

      <hr />
      <p class="center">
        <a class="btn btn-large" href="#availability">
          <?php echo JText::_('COM_ACCOMMODATION_SITE_CHECK_AVAILABILITY'); ?>  
        </a>
        <a class="btn btn-primary btn-large" href="#email">
          <?php echo JText::_('COM_ACCOMMODATION_SITE_CONTACT_OWNER'); ?>  
        </a>
      </p>
    </div>  
  </div> 

  <div class="span7">
    <!-- Image gallery -->
    <!-- Needs go into a separate template -->
    <div id="main" role="main">
      <section class="slider">
        <div id="slider" class="flexslider">
          <ul class="slides">
            <?php foreach ($this->images as $images => $image) : ?> 
              <li>
                <?php if ($this->item->parent_id != 1) : ?>  
                  <img src="<?php echo JURI::root() . 'images/' . $this->item->parent_id . '/gallery/' . str_replace('.', '_550x375.', $image->image_file_name); ?>
                       " /> 
                     <?php else: ?>
                  <img src="<?php echo JURI::root() . 'images/' . $this->item->id . '/gallery/' . str_replace('.', '_550x375.', $image->image_file_name); ?>
                       " /> 
                     <?php endif; ?>
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

                <?php if ($this->item->parent_id != 1) : ?>  
                  <img src="<?php echo JURI::root() . 'images/' . $this->item->parent_id . '/thumbs/' . $image->image_file_name ?>" /> 
                <?php else: ?>
                  <img src="<?php echo JURI::root() . 'images/' . $this->item->id . '/thumbs/' . $image->image_file_name ?>" /> 
                <?php endif; ?>

              </li>     
            <?php endforeach; ?>
          </ul>
        </div>
      </section>
    </div>
  </div>
</div>
<div class="row-fluid" id="description">
  <div class="span12">
    <?php echo $this->loadTemplate('navigator'); ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span8">
    <?php if ($this->item->title) : ?>
      <h2><?php echo JText::sprintf('HOLIDAY_ACCOMMODATION_AT', $this->item->title) ?></h2>  
    <?php endif; ?>
    <?php if ($this->item->description) : ?>
      <?php echo $this->item->description; ?>
    <?php endif; ?>
  </div>
</div>

<div class="row-fluid" id="location">
  <div class="span12">
    <?php echo $this->loadTemplate('navigator'); ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span8">
    <?php if ($this->item->title) : ?>
      <h2><?php echo JText::sprintf('COM_ACCOMMODATION_ABOUT_ACCOMMODATION_IN', $this->item->nearest_town, $this->item->department_as_text, 'Region') ?></h2>  
    <?php endif; ?>
    <?php if ($this->item->location_details) : ?>
      <?php echo $this->item->location_details; ?>
    <?php endif; ?>
    <div id="map_canvas" style="width:100%; height:370px"></div>
  </div>
</div>

<!--<div class="row-fluid" id="travel">
  <div class="span12">-->
<?php //echo $this->loadTemplate('navigator');  ?>
<!--</div>
</div>-->

<div class="row-fluid">
  <div class="span8">
    <?php if ($this->item->title) : ?>
      <h3><?php echo htmlspecialchars(JText::_('COM_ACCOMMODATION_HOW_TO_GET_TO_ACCOMMODATION_IN')) ?></h3>  
    <?php endif; ?>
    <?php if ($this->item->getting_there) : ?>
      <?php echo $this->item->getting_there; ?>
    <?php endif; ?>
  </div>
  <div class="span4"> 

  </div>
</div>

<!--<div class="row-fluid" id="activities">
  <div class="span12">-->
<?php //echo $this->loadTemplate('navigator');  ?>
<!--</div>
</div>-->
<div class="row-fluid">
  <div class="span8">
    <?php if ($this->item->title) : ?>
      <h3><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_ACTIVITIES_AT', $this->item->title)) ?></h3> 
    <?php endif; ?>
    <?php if ($this->item->activities_other) : ?>
      <?php echo $this->item->activities_other; ?>
    <?php endif; ?>      
  </div>
  <div class="span4"> 

  </div>
</div>
<?php if ($this->reviews) { ?>
  <div class="row-fluid" id="reviews">
    <div class="span12">
      <?php echo $this->loadTemplate('navigator');
      ?>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span8">
      <?php if ($this->item->title) : ?>
        <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_REVIEWS_AT', $this->item->title)) ?></h2> 
      <?php endif; ?>

      <?php foreach ($this->reviews as $review) : ?>
        <blockquote>
          <?php echo $review->review_text; ?>
          <small>
            <?php echo $review->guest_name; ?>
            <cite title="<?php echo JText::_('COM_ACCOMMODATION_SITE_DATE_OF_STAY'); ?>">
              <?php echo $review->date; ?>
            </cite>  
          </small>
        </blockquote>         
      <?php endforeach; ?>
    </div>
    <div class="span4"></div>
  </div>
<?php } ?>
<div class="row-fluid" id="facilities">
  <div class="span12">
    <?php echo $this->loadTemplate('navigator');
    ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span-12">
    <?php if ($this->item->title) : ?>
      <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_FACILITIES_AT', $this->item->title)) ?></h2> 
    <?php endif; ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span8">
    <table class="table table-striped">

      <?php if ($this->item->property_type) : ?>    
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
      <?php if ($this->item->bedrooms && $this->item->bedrooms > 0) { ?>    
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_BEDROOMS') ?></td>
          <td>
            <?php if ($this->item->single_bedrooms) : echo JText::sprintf('COM_ACCOMMODATION_SITE_SINGLE_BEDROOMS', $this->item->single_bedrooms);
            endif; ?>
            <?php if ($this->item->double_bedrooms) : echo JText::sprintf('COM_ACCOMMODATION_SITE_DOUBLE_BEDROOMS', $this->item->double_bedrooms);
            endif; ?>
            <?php if ($this->item->triple_bedrooms) : echo JText::sprintf('COM_ACCOMMODATION_SITE_TRIPLE_BEDROOMS', $this->item->triple_bedrooms);
            endif; ?>
  <?php if ($this->item->quad_bedrooms) : echo JText::sprintf('COM_ACCOMMODATION_SITE_QUAD_BEDROOMS', $this->item->quad_bedrooms);
  endif; ?>
        <?php if ($this->item->twin_bedrooms) : echo JText::sprintf('COM_ACCOMMODATION_SITE_TWIN_BEDROOMS', $this->item->twin_bedrooms);
        endif; ?>
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
      <?php if (array_key_exists('Suitability', $this->facilities)) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_SUITABILITY') ?></td>
          <td><?php echo implode(',', $this->facilities['Suitability']) ?></td>         
        </tr>
<?php endif; ?>

      <!-- Linen costs -->  
      <?php if ($this->item->linen_costs) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_LINEN_COSTS') ?></td>
          <td><?php echo $this->item->linen_costs; ?></td>        
        </tr>      
<?php endif; ?>       


      <?php if (array_key_exists('Internal facilities', $this->facilities)) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_INTERNAL') ?></td>
          <td><?php echo implode(',', $this->facilities['Internal facilities']) ?></td>         
        </tr>      
<?php endif; ?>   

      <?php if ($this->item->internal_facilities_other) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES__OTHER_INTERNAL') ?></td>
          <td><?php echo $this->item->internal_facilities_other; ?></td>        
        </tr>      
<?php endif; ?>       
      <?php if (array_key_exists('External features', $this->facilities)) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_EXTERNAL') ?></td>
          <td><?php echo implode(',', $this->facilities['External features']) ?></td>         
        </tr>      
<?php endif; ?>   
      <?php if ($this->item->external_facilities_other) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_FACILITITES_OTHER_EXTERNAL') ?></td>
          <td><?php echo $this->item->external_facilities_other; ?></td>        
        </tr>      
<?php endif; ?>       
      <?php if (array_key_exists('Kitchen features', $this->facilities)) : ?>
        <tr>
          <td><?php echo JText::_('COM_ACCOMMODATION_SITE_KITCHEN_FEATURES') ?></td>
          <td><?php echo implode(',', $this->facilities['Kitchen features']) ?></td>         
        </tr>      
<?php endif; ?>       


    </table>

  </div>
  <div class="span4">
  </div>
</div>




<div class="row-fluid" id="availability">
  <div class="span12">
    <?php echo $this->loadTemplate('navigator'); ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span12">
<?php if ($this->item->title) : ?>
      <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_AVAILABILITY_AT', $this->item->title)) ?></h2> 
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
</div>
<div class="row-fluid">
  <div class="span8">
<?php if ($this->availability) : ?>
  <?php echo $this->availability; ?>
<?php endif; ?>
  </div>
  <div class="span4">
    <h4><?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_KEY')?></h4>
    <table class="key">
      <tr>
        <td class="available"></td>
        <td>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_KEY_AVAILABLE')?></td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
        <td class="unavailable">&nbsp;</td>
        <td>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_KEY_UNAVAILABLE')?></td>
      </tr>
    </table>    
  </div>
</div>

<div class="row-fluid" id="tariffs">
  <div class="span12">
    <?php echo $this->loadTemplate('navigator'); ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span12">
<?php if ($this->item->title) : ?>
      <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_TARIFFS_AT', $this->item->title)) ?></h2> 
    <?php endif; ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span8">
<?php if ($this->tariffs) : ?>
      <?php echo $this->loadTemplate('tariffs'); ?>
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
  <div class="span12">
    <?php echo $this->loadTemplate('navigator'); ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span12">
<?php if ($this->item->title) : ?>
      <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_EMAIL_THE_OWNER', $this->item->title)) ?></h2> 
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
    
    <?php if ($this->item->website) : ?>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_CONTACT_WEBSITE'); ?>
      <a target="_blank" rel="nofollow" href="<?php echo JRoute::_('index.php?option=com_accommodation&task=property.viewsite&id=' . ($this->item->parent_id == 1 ? $this->item->id : $this->item->parent_id)) . '&' . JSession::getFormToken() . '=1';?>">
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
<script>
  jQuery(document).ready(function() {
 
    initialize();
  });

  function initialize() {
    var myLatLng = new google.maps.LatLng(<?php echo $this->item->latitude ?>,  <?php echo $this->item->longitude ?>);
    var myOptions = {
      center: myLatLng,
      zoom: 6,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: true,
      zoomControl:true
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var marker = new google.maps.Marker({
      position: myLatLng,
      map:map,
      title:"<?php echo $this->item->title ?>"
    });
    google.maps.event.addListener(map, 'zoom_changed', function() {
      // 3 seconds after the center of the map has changed, pan back to the
      // marker.
      window.setTimeout(function() {
        map.panTo(marker.getPosition());
      }, 3000);
    });
  }    
 

</script>



