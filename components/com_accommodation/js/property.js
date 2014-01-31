jQuery(window).load(function(){
  loadGoogleMaps();
  !function(d,s,id){
    var js,fjs=d.getElementsByTagName(s)[0];
    if(!d.getElementById(id)){
      js=d.createElement(s);
      js.id=id;
      js.src="https://platform.twitter.com/widgets.js";
      fjs.parentNode.insertBefore(js,fjs);
    }
  }(document,"script","twitter-wjs");
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
})


jQuery(document).ready(function() {
 jQuery('.shortlist').each(function() { // For each result

    // Get the data-action state
    jQuery(this).popover({// Initialise a popover
      trigger: 'manual' // Take control of when the popover is opened
    }).click(function(event) {

      event.preventDefault(); // Prevent the default click behaviour
      jQuery('.shortlist').not(this).popover('hide'); // Hide any other popovers that are open
      popover = jQuery(this).data('popover'); // Get the popover data attributes
      popover.options.content = getContent(this); // Update the content by calling getContent
      jQuery(this).popover('toggle'); // Manually open the popover 
    });

  })
  // The slider being synced must be initialized first
  jQuery('#carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    itemWidth: 100,
    itemMargin: 5,
    asNavFor: '#slider',
    allowOneSlide: false
  });

  jQuery('#slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    sync: "#carousel",
    video: "true",
    minItems: 1
  });


});

function getContent(that) {

  action = jQuery(that).data('action');

  if (action == 'remove') {
    return "<span class=\'icon icon-checkbox\'>&nbsp;Shortlist</span><hr /><a href=\'/shortlist\'>View shortlist</a>";

  }
  return "<span class=\'icon icon-checkbox-unchecked\'>&nbsp;Shortlist</span><hr /><a href=\'/shortlist\'>View shortlist</a>";


}

function loadGoogleMaps() {
  var script = document.createElement('script');
  script.type = 'text/javascript';
  
  script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBudTxPamz_W_Ou72m2Q8onEh10k_yCwYI&sensor=true&' +
  'callback=initialize';
  document.body.appendChild(script);
}

function initialize() {
  var data = jQuery('#map_canvas').data();
  var lat = data.lat;
  var lon = data.lon;
  var myLatLng = new google.maps.LatLng(lat,lon);
  var myOptions = {
    center: myLatLng,
    zoom: 6,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    disableDefaultUI: true,
    zoomControl: true
  };
  var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  var marker = new google.maps.Marker({
    position: myLatLng,
    map: map,
    title: "<?php echo $this->item->unit_title ?>"
  });
  google.maps.event.addListener(map, 'zoom_changed', function() {
    // 3 seconds after the center of the map has changed, pan back to the
    // marker.
    window.setTimeout(function() {
      map.panTo(marker.getPosition());
    }, 3000);
  });
}


  