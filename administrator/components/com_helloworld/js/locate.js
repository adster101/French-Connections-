jQuery(document).ready(function() {

  if (jQuery('#map').length) {
    initialise();
  }



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

  if (lat == '0') {
    lat = '46.589069';
  }
  if (lon == '0') {
    lon = '2.416992';
  }

  var myLatLng = new google.maps.LatLng(lat, lon);

  var mapOptions = {
    center: myLatLng,
    zoom: 6,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var map = new google.maps.Map(document.getElementById("map"),
          mapOptions);

  var marker = new google.maps.Marker({
    position: myLatLng,
    map: map,
    title: "Woot!",
    draggable: true
  })



  google.maps.event.addListener(marker, "dragend", function() {

    map.panTo(marker.getPosition());
    LatLng = marker.getPosition();

    // Update lat and long for the property...    
    document.getElementById('jform_latitude').value = LatLng.lat().toFixed(6);
    document.getElementById('jform_longitude').value = LatLng.lng().toFixed(6);

    // Do an ajax call to populate the list of nearest towns...
    jQuery.getJSON("/administrator/index.php?option=com_helloworld&task=helloworld.nearestpropertylist",
            {
              lat: LatLng.lat().toFixed(6),
              lon: LatLng.lng().toFixed(6),
              format: "json"
            },
    function(data) {
      var options = '';
      for (var i = 0; i < data.length; i++) {
        options += '<option value="' + data[i].id + '">' + data[i].title + '</option>';
      }
      jQuery("select#jform_city").html(options);

    }
    );
  });

  google.maps.event.addListener(map, "zoom_changed", function() {
    map.panTo(marker.getPosition());


  });
}

function updatepropertylist()
{

  // Get the user ID for the property
  var id = jQuery('#jform_created_by_id').attr('value');


  jQuery.getJSON("/administrator/index.php?option=com_helloworld&task=helloworld.nearestpropertylist",
          {
            id: id,
            format: "json"
          },
  function(data) {
    var options = '';
    for (var i = 0; i < data.length; i++) {
      options += '<option value="' + data[i].id + '">' + data[i].title + '</option>';
    }
    jQuery("select#jform_parent_id").html(options);

  }
  );


}
      