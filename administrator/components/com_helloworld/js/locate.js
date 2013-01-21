jQuery(document).ready(function(){
  initialise();
})

/*
 * Simple google maps code to allow the user to choose the location of their property from the map.
 * 
 * Pretty self explanatory really
 *
 **/  

function initialise() {

  var lat = document.getElementById('jform_latitude').value;
  var lon = document.getElementById('jform_longitude').value;

  if (lat == '') {
    lat = '46.589069';
  }
  if (lon == '') {
    lon = '2.416992';
  }

  var myLatLng = new google.maps.LatLng(lat,lon);

  var mapOptions = {
    center: myLatLng,
    zoom: 6,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var map = new google.maps.Map(document.getElementById("map"),
    mapOptions);

  var marker = new google.maps.Marker({
    position:myLatLng,
    map:map,
    title:"Woot!",
    draggable: true
  })

  google.maps.event.addListener(marker, "dragend", function() {
    
    map.panTo(marker.getPosition());
    LatLng = marker.getPosition();
    
    // Update lat and long for the property...    
    document.getElementById('jform_latitude').value = LatLng.lat().toFixed(6);
    document.getElementById('jform_longitude').value = LatLng.lng().toFixed(6);
    
    // Do an ajax call to populate the list of nearest towns...
    
    
    
  });

  google.maps.event.addListener(map, "zoom_changed", function() {
    map.panTo(marker.getPosition());
  });


    
  


  

} 
      