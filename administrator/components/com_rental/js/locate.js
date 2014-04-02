var map = '';
var marker = '';
jQuery(document).ready(function() {

  if (jQuery('#map').length) {
    initialise();
  }

  jQuery('#jform_department').on('change', function(e) {

    var dept = jQuery(this).val();
    var userToken = document.getElementsByTagName("input")[0].name;

    // Do an ajax call to populate the list of nearest towns...
    jQuery.getJSON("/administrator/index.php?option=com_rental&task=NearestTowns.DepartmentTowns&" + userToken + "=1",
            {
              dept: dept,
              format: "json"
            },
    function(data) {
      var choose = Joomla.JText._('COM_RENTAL_PLEASE_CHOOSE');
      var options = '<option value="">' + choose + '</option>';
      for (var i = 0; i < data.length; i++) {
        options += '<option value="' + data[i].id + '" data-latitude="' + data[i].latitude + '" data-longitude="' + data[i].longitude + '">' + data[i].title + '</option>';
      }

      jQuery("select#jform_city").html(options);
    });
  })

  jQuery('#jform_city').on('change', function(e) {

    var selected = jQuery(this).find('option:selected');
    var latitude = selected.data('longitude'); // Errr, lat and long are mixed up in the classifications table
    var longitude = selected.data('latitude');
    var bounds = new google.maps.LatLngBounds();
    var LatLng = new google.maps.LatLng(latitude, longitude);
    var bounds = new google.maps.LatLngBounds();

    marker.setPosition(LatLng);
    map.panTo(LatLng);
    map.setZoom(14);

    // Update lat and long for the property...    
    document.getElementById('jform_latitude').value = LatLng.lat().toFixed(6);
    document.getElementById('jform_longitude').value = LatLng.lng().toFixed(6);


  })

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

  if (lat == '0' && lon == '0') {
    lat = '46.589069';
    lon = '2.416992';
    zoom = 6;
  } else {
    zoom = 14;
  }

 

  var myLatLng = new google.maps.LatLng(lat, lon);

  var mapOptions = {
    center: myLatLng,
    zoom: zoom,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  map = new google.maps.Map(document.getElementById("map"),
          mapOptions);

  marker = new google.maps.Marker({
    position: myLatLng,
    map: map,
    title: "Drag me...",
    draggable: true
  })



  google.maps.event.addListener(marker, "dragend", function() {

    map.panTo(marker.getPosition());
    LatLng = marker.getPosition();

    // Update lat and long for the property...    
    document.getElementById('jform_latitude').value = LatLng.lat().toFixed(6);
    document.getElementById('jform_longitude').value = LatLng.lng().toFixed(6);


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
      