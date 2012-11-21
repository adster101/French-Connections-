<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$language = JFactory::getLanguage();
$lang = $language->getTag();
$price_range = array();
foreach ($this->tariffs as $tariff) {
  $price_range[] = $tariff->tariff;
}
print_r($this->images);
?>

<div class="row-fluid">
  <div class="span12">
    <?php echo $this->loadTemplate('crumbs'); ?>
  </div>  
</div>
<div class="page-header">
  <h1>
<?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_TITLE', $this->item->title, $this->item->property_type, $this->item->nearest_town, $this->item->department) ?>
  </h1>
</div>
<div class="row-fluid">
  <div class="span4 key-facts">
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
        <strong><?php echo min($price_range) . '-' . max($price_range) ?></strong> 
      </span>
<?php
if ($this->item->base_currency) {
  echo htmlspecialchars($this->item->base_currency);
}

if ($this->item->tariffs_based_on) {
  echo '&nbsp;' . htmlspecialchars($this->item->tariffs_based_on);
}
?>
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
    <p>
      <strong><?php echo JText::_('COM_ACCOMMODATION_SITE_ACCESS_OPTIONS'); ?></strong>
    </p>
  </div> 
  <div class="span8">
    <p><img src="/images/75/gallery/Lighthouse.jpg"</p>	
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
  <div class="span4">
    <h3><?php echo JText::_('COM_ACCOMMODATION_SITE_WHERE_IS_IT'); ?></h3>
    <div id="map_canvas" style="width:100%; height:370px"></div>
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
      <h2><?php echo JText::sprintf('COM_ACCOMMODATION_ABOUT_ACCOMMODATION_IN', $this->item->nearest_town, $this->item->department, 'Region') ?></h2>  
    <?php endif; ?>
    <?php if ($this->item->location_details) : ?>
      <?php echo $this->item->location_details; ?>
    <?php endif; ?>

    <?php if ($this->item->title) : ?>
      <h2><?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_HOW_TO_GET_TO_ACCOMMODATION_IN', $this->item->nearest_town, 'Department', 'Area')) ?></h2>  
    <?php endif; ?>
    <?php if ($this->item->getting_there) : ?>
      <?php echo $this->item->getting_there; ?>
    <?php endif; ?>
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
      <strong>
    <?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_LAST_UPDATED_ON') ?>
      </strong>
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
    <p>Accommodation key</p>
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



