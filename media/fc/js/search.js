var infowindow;

jQuery(document).ready(function() {


  jQuery('#sort_by').on('change', function() {
    alert('Poop');
  });

  // Works on the tabs on the search results page. Needs to be made more generic
  jQuery('a[data-toggle="tab"]').on('shown', function(e) {

    jQuery('#map_canvas').hide();

    if (!window.google) {
      loadScript(); // Asych load the google maps stuff
    }
    // Store the selected tab #ref in local storage, IE8+
    localStorage['selectedTab'] = jQuery(e.target).attr('href');

    // Get the selected tab from the local storage
    var selectedTab = localStorage['selectedTab'];
    var infowindow;

    // If the selected tab is the map tag then grab the markers
    if (selectedTab == '#mapsearch') {

      var path = window.location.pathname.replace('/accommodation/', '');

      // Do an ajax call to get a list of towns...
      jQuery.getJSON("/index.php?option=com_fcsearch&task=mapsearch.markers&format=json", {
        s_kwds: path
      },
      function(data) {

        // Get the map instance
        map = document.map;

        markers = {};

        // Loop over all data (properties) and create a new marker
        for (var i = 0; i < data.length; i++) {

          // The lat long of the propert, units will appear stacked on top...
          var myLatlng = new google.maps.LatLng(data[i].latitude, data[i].longitude);

          // Create the marker instance
          marker = new google.maps.Marker({
            position: myLatlng,
            map: map
          });

          marker.setTitle((i + 1).toString());
          content = '<div class="media"><a class="pull-left" href="' + data[i].link + '"><img class="media-object" src="' + data[i].thumbnail + '"/></a><div class="media-body"><h4 class="media-heading"><a href="' + data[i].link + '">' + data[i].unit_title + '</a></h4><p>' + data[i].description + '</p></div></div>';
          attachContent(marker, content);

          markers[i] = marker;

          //  Create a new viewpoint bound, so we can centre the map based on the markers
          var bounds = new google.maps.LatLngBounds();

          //  Go through each...
          jQuery.each(markers, function(index, marker) {
            bounds.extend(marker.position);
          });

          //  Fit these bounds to the map
          map.fitBounds(bounds);
        }
      });
    }

    jQuery('#map_canvas').show();

  });




  // Get the selected tab, if any and set the tab accordingly...
  var selectedTab = localStorage['selectedTab'];

  if (selectedTab == '#mapsearch') {
    jQuery('.nav li a[href="' + selectedTab + '"]').tab('show');
  }

  jQuery('#property-search-button').on('click', function() {

    event.preventDefault();

    var path = getPath();

    // Amend the path that the form is submitted to
    jQuery('form#property-search').attr('action', path);

    // Submit the form
    jQuery('form#property-search').submit();

  });

  // Bind the typeahead business
  jQuery(".typeahead").typeahead({
    source: function(query, process) {
      jQuery.get('/index.php?option=com_fcsearch&task=suggestions.display&format=json&tmpl=component',
              {
                q: query,
                items: 10
              },
      function(data) {
        process(data);
      }
      )
    }
  })


  jQuery('.shortlist-login').on('click', function() {

    jQuery('#myModal').modal({
      remote: '/my-account?tmpl=component&layout=modal'
    });

  });



  jQuery('.shortlist-manage').each(function() {
    
    jQuery(this).popover();
    
    jQuery(this).on('click', function(event){
      event.preventDefault();
    });

  });
  
  

  jQuery(".show").click(function(event) {
    event.preventDefault();
    jQuery(this).prev().prev().toggleClass('show');
  })

}) // End of on DOM ready

function success(data) {

}

function getPath(event) {

  // val is the 'active' suggestion populated by typeahead
  // e.g. the option chosen should be the last active one
  //var val = jQuery(".typeahead.dropdown-menu").find('.active').attr('data-value');

  // The value contained in the typeahead field
  var chosen = jQuery(".typeahead").attr('value');

  // Double check that the typeahead has any elements, if not then it means it's already populated, e.g. when you land on a search results page
  //var count = jQuery(".typeahead.dropdown-menu li").length;

  // A regex to check for an integer
  //var intRegex = new RegExp('^\\d+$');

  //if (intRegex.test(chosen)) {
  // Let it through
  //return true;
  //} else if (chosen !== '' && count !== 0) {
  //if (val !== chosen) {
  //jQuery('#myModal').modal();
  //event.preventDefault();
  //return false;
  //}
  //} else if (chosen === '') { // otherwise, just check that the chosen field isn't empty...check the q var on the server side
  //jQuery('#myModal').modal();
  //event.preventDefault();
  //return false;
  //}

  if (chosen === '') {
    jQuery(".typeahead").attr('value', 'france');

  }

  // The path of the search, e.g. /search or /fr/search
  var path = '/accommodation';

  // Let's get all the form input elements - more performant to do it in one go rather than getting each via a separate DOM lookup
  var inputs = jQuery('#property-search').find(':input');

  // Get each of the values we're interested in adding to the search string
  var s_kwds = inputs.filter('#s_kwds').prop('value');
  var sort_by = inputs.filter('#sort_by').prop('value');
  var min_price = inputs.filter('#min_price').prop('value');
  var max_price = inputs.filter('#max_price').prop('value');
  var occupancy = inputs.filter('#occupancy').prop('value');
  var bedrooms = inputs.filter('#bedrooms').prop('value');
  var arrival = inputs.filter('#arrival').prop('value');
  var departure = inputs.filter('#departure').prop('value');

  path = path + '/' + stripVowelAccent(s_kwds);

  if (arrival !== '') {
    path = path + '/arrival_' + arrival;
  }

  if (departure !== '') {
    path = path + '/departure_' + departure;
  }
  if (occupancy !== '') {
    path = path + '/occupancy_' + occupancy;
  }

  if (bedrooms !== '') {
    path = path + '/bedrooms_' + bedrooms;
  }

  // These fields are not present on the homepage search so they can be undefined as well as empty
  if (sort_by !== '' && typeof(sort_by) !== 'undefined') {
    path = path + '/' + sort_by;
  }

  if (min_price !== '' && typeof(min_price) !== 'undefined') {
    path = path + '/' + min_price;
  }

  if (max_price !== '' && typeof(max_price) !== 'undefined') {
    path = path + '/' + max_price;
  }

  return path;


}

function initmap() {


  jQuery('#map_canvas').css('width', '100%');
  jQuery('#map_canvas').css('height', '600px');
  var myLatLng = new google.maps.LatLng(46.8, 2.8);
  var myOptions = {
    center: myLatLng,
    zoom: 6,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    disableDefaultUI: true,
    zoomControl: true
  }
  var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  document.map = map;
}

// The five markers show a secret message when clicked
// but that message is not within the marker's instance data
function attachContent(marker, num) {
  google.maps.event.addListener(marker, 'click', function() {

    if (infowindow)
      infowindow.close();

    infowindow = new google.maps.InfoWindow({
      content: num,
      maxWidth: 300
    });
    infowindow.open(marker.get('map'), marker);
  });
}

function loadScript() {
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBudTxPamz_W_Ou72m2Q8onEh10k_yCwYI&sensor=true&' +
          'callback=initmap';
  document.body.appendChild(script);
}

// Function removes and replaces all French accented characters with non accented characters
// as well as removing spurious characters and replacing spaces with dashes...innit!
function stripVowelAccent(str) {

  var s = str;
  var rExps = [
    /[\xC0-\xC2]/g,
    /[\xE0-\xE2]/g,
    /[\xC8-\xCA]/g,
    /[\xE8-\xEB]/g,
    /[\xCC-\xCE]/g,
    /[\xEC-\xEE]/g,
    /[\xD2-\xD4]/g,
    /[\xF2-\xF4]/g,
    /[\xD9-\xDB]/g,
    /[\xF9-\xFB]/g
  ];

  var repChar = ['A', 'a', 'E', 'e', 'I', 'i', 'O', 'o', 'U', 'u'];

  for (var i = 0; i < rExps.length; i++) {
    s = s.replace(rExps[i], repChar[i]);
  }

  // Replace braces with spaces
  s = s.replace(/[()]/g, ' ');

  // Replace apostrophe with space
  s = s.replace(/[\']/g, ' ');

  if (typeof String.prototype.trim !== 'function') {
    String.prototype.trim = function() {
      return this.replace(/^\s+|\s+$/g, '');
    }
  }

  // Trim any trailing space
  s = s.trim();

  // Remove any duplicate whitespace, and ensure all characters are alphanumeric
  s = s.replace(/(\s|[^A-Za-z0-9\-])+/g, '-');



  s = s.toLowerCase();

  return s;
}