jQuery(window).load(function() {
  // Load the google maps crap
  loadGoogleMaps();

  // Load the twitter crap
  !function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (!d.getElementById(id)) {
      js = d.createElement(s);
      js.id = id;
      js.src = "https://platform.twitter.com/widgets.js";
      fjs.parentNode.insertBefore(js, fjs);
    }
  }

  // Load the Facebook crap
  (document, "script", "twitter-wjs");
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id))
      return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Done loading crap

})

jQuery(document).ready(function() {
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
  var myLatLng = new google.maps.LatLng(lat, lon);
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
    map: map
  });
  google.maps.event.addListener(map, 'zoom_changed', function() {
    // 3 seconds after the center of the map has changed, pan back to the
    // marker.
    window.setTimeout(function() {
      map.panTo(marker.getPosition());
    }, 1500);
  });
}  