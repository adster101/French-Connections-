jQuery(window).load(function() {
  if (jQuery('#property_map_canvas').length) {
    loadGoogleMaps('initPropertyMap');
  }
})

function initPropertyMap() {

  var data = jQuery('#property_map_canvas').data();
  var lat = data.lat;
  var lon = data.lon;
  var hash = data.hash;
  var myLatLng = new google.maps.LatLng(lat, lon);
  var myOptions = {
    center: myLatLng,
    zoom: 9,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    disableDefaultUI: true,
    zoomControl: true
  };

  var map = new google.maps.Map(document.getElementById("property_map_canvas"), myOptions);


  var prnmarker = new google.maps.Marker({
    position: myLatLng,
    map: map,
    icon: '/images/mapicons/iconflower.png'

  });

  // Do an ajax call to get a list of towns...
  jQuery.getJSON("/index.php?option=com_accommodation&task=populatemap.getItems&format=json&" + hash + "=1", {
    lat: lat,
    lon: lon
  },
  function(data) {



    markers = {};

    // Loop over all data (properties) and create a new marker
    for (var i = 0; i < data.length; i++) {

      // The lat long of the propert, units will appear stacked on top...
      var myLatlng = new google.maps.LatLng(data[i].latitude, data[i].longitude);

      // Create the marker instance
      marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        icon: '/images/mapicons/iconplaceofinterest.png'

      });

      marker.setTitle((i + 1).toString());
      content = '<div class="media"><div class="media-body"><h4 class="media-heading"><a href="' + data[i].link + '">' + data[i].title + '</a></h4><p>' + data[i].description + '</p></div></div>';
      attachContent(marker, content, 175);

      markers[i] = marker;


    }

    //  Create a new viewpoint bound, so we can centre the map based on the markers
    var bounds = new google.maps.LatLngBounds();

    //  Go through each...
    jQuery.each(markers, function(index, marker) {
      bounds.extend(marker.position);
    });
    
    bounds.extend(prnmarker.position);

    //  Fit these bounds to the map
    map.fitBounds(bounds);


  }).fail(function(e) {
  }).always(function() {

  });


}  