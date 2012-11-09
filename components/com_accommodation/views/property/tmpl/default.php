<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$language = JFactory::getLanguage();
$lang = $language->getTag();
// Load the appropriate language file
$language->load('COM_ACCOMMODATION', JPATH_ADMINISTRATOR, $lang, true);
?>
<div class="page-header">
  <h1><?php echo $this->item->greeting; ?>
    <?php echo JText::_('COM_ACCOMMODATION_SITE_HOLIDAY_RENTAL_IN') . '&nbsp;' . $this->item->nearest_town . ',&nbsp;' . $this->item->title . ',' ?>
    <?php echo JText::_('COM_ACCOMMODATION_SITE_FRANCE'); ?>
  </h1>
</div>
<div class="row-fluid">
  <div class="span4 key-facts">
    <div class="row-fluid">
      <div class="span6">
        <p><a class="btn btn-small" href="#">
            <i class="icon-heart"> </i><?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_TO_FAVOURITES') ?></a>
        </p>
      </div>
      <div class="span6">
        <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style">	
          <p class="clearfix"><a class="addthis_button_preferred_4 addthis_button_print at300b" title="Print" href="#">
              <span class="at16nc at300bs at15nc at15t_print at16t_print">
                <span class="at_a11y">Share on print</span>
              </span>
            </a>
            <a class="addthis_button_preferred_1 addthis_button_facebook at300b" title="Send to Facebook" href="#">
              <span class="at16nc at300bs at15nc at15t_facebook at16t_facebook">
                <span class="at_a11y">Share on facebook</span>
              </span>
            </a>
            <a class="addthis_button_preferred_2 addthis_button_twitter at300b" title="Tweet This" href="#">
              <span class="at16nc at300bs at15nc at15t_twitter at16t_twitter">
                <span class="at_a11y">Share on twitter</span>
              </span>
            </a>
            <a class="addthis_button_preferred_3 addthis_button_email at300b" title="Email" href="#">
              <span class="at16nc at300bs at15nc at15t_email at16t_email">
                <span class="at_a11y">Share on email</span>
              </span>
            </a>
            <a class="addthis_button_compact at300m" href="#">
              <span class="at16nc at300bs at15nc at15t_compact at16t_compact">
                <span class="at_a11y">More Sharing Services</span>
              </span>
            </a>
            <a class="addthis_counter addthis_bubble_style" style="display: block; " href="#">
              <a class="addthis_button_expanded" title="View more services" href="#"></a>
              <a class="atc_s addthis_button_compact">
                <span></span></a></a></p>
          <div class="atclear"></div></div>
        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=frenchconnections"></script>
        <!-- AddThis Button END -->	
      </div>
    </div>	
    <p>
      <span class="lead">
        <strong>£560 - £735</strong> 
      </span>
      <?php echo JText::_('COM_ACCOMMODATION_SITE_RATE_PER') ?>
    </p>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_SITE_OCCUPANCY'); ?>
      <span class="pull-right"><?php echo $this->item->occupancy; ?></span>
    </p>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_SITE_BEDROOMS'); ?>
      <span class="pull-right"><?php echo $this->item->bedrooms; ?></span>
    </p>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_SITE_BATHROOMS'); ?>
      <span class="pull-right"><?php echo $this->item->bathrooms; ?></span>
    </p>
    <p>
      <?php echo JText::_('COM_ACCOMMODATION_SITE_TOILETS'); ?>
      <span class="pull-right"><?php echo $this->item->toilets; ?></span>
    </p>
    <p><strong><?php echo JText::_('COM_ACCOMMODATION_SITE_ACCESS_OPTIONS'); ?></strong>
    </p>
  </div> 
  <div class="span8">
    <p><img src="/images/75/gallery/Lighthouse.jpg"</p>	
  </div>
</div>
<div class="row-fluid">
  <div class="span12">
    <?php echo $this->loadTemplate('navigator'); ?>
  </div>
</div>
<div class="row-fluid">
  <div class="span8">
    <?php echo $this->item->description; ?>
  </div>
  <div class="span4">
    <h3><?php echo JText::_('COM_ACCOMMODATION_SITE_WHERE_IS_IT'); ?></h3>
    <div id="map_canvas" style="width:100%; height:370px"></div>
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
      zoom: 8,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: true,
      zoomControl:true
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var marker = new google.maps.Marker({
      position: myLatLng,
      map:map,
      title:"<?php echo $this->item->greeting ?>"
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



