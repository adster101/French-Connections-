var infowindow;

jQuery(document).ready(function() {

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

  jQuery('#property-search-button').on('click', function(event) {

    event.preventDefault();

    var path = getPath();

    // Amend the path that the form is submitted to
    jQuery('form#property-search').attr('action', path);

    // Submit the form
    jQuery('form#property-search').submit();

  });

  jQuery('#sort_by').on('change', function(event) {

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


  jQuery('.shortlist-login').on('click', function(event) {

    // TO DO - add the property clicked on to the shortlist in the background...
    jQuery('#myModal').modal({
      remote: '/my-account?tmpl=component&layout=modal'
    });

  });

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

  jQuery('.search-results').on('click', '.popover span', function(ev) { // When a pop over span is clicked
    var el = jQuery(this);
    var favourite = el.parent().parent().siblings('a');
    var dataObj = favourite.data(); // Get the data attributes of the parent a element
    var url_params = {};
    var userToken = document.getElementsByTagName("input")[0].name;

    url_params.id = dataObj.id;
    url_params.action = dataObj.action;


    var url = 'index.php?option=com_shortlist&task=shortlist.update&tmpl=component&' + userToken + '=1';
    jQuery.ajax({
      dateType: "json",
      url: url,
      data: url_params
    }).done(function(data) {

      if (data == 1) {
        dataObj.action = (dataObj.action === 'add') ? 'remove' : 'add'; // action is the state the object is changing *to* not what what it is now...
        favourite.data(dataObj);

        if (dataObj.action == 'remove') {
          el.addClass('icon-checkbox');
          el.removeClass('icon-checkbox-unchecked');
        } else {
          el.addClass('icon-checkbox-unchecked');
          el.removeClass('icon-checkbox');
        } // If action is remove then add icon-checkbox else remove it
        (dataObj.action == 'remove') ? favourite.toggleClass('muted', false) : favourite.toggleClass('muted', true); // If action is remove then add icon-checkbox else remove it
        favourite.attr('data-action', dataObj.action);

      } else {
        jQuery('.shortlist').addClass('muted');
        el.removeClass('icon-checkbox icon-checkbox-unchecked').html('<p>Session expired.<br /> Please login.</p>');
      }
    })
  });

  // Deal with the more/less options for the refine search bit.
  jQuery(".show").click(function(event) {

    // Get the containing element that we want to show/hide
    var el = jQuery(this).prev().prev();

    // Prevent the default click behaviour
    event.preventDefault();

    // Toggle the containing class 
    el.toggleClass('show');

    // Check the open/closed state
    if (el.hasClass('show')) { // Must be 'open' so we want to show less options text
      jQuery(this).text(Joomla.JText.COM_FCSEARCH_SEARCH_SHOW_LESS_OPTIONS);
    } else if (el.hasClass('hide')) { // Must be 'closed' so we want to show more options texts
      jQuery(this).text(Joomla.JText.COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS);
    }
  })

}) // End of on DOM ready

function getContent(that) {

  action = jQuery(that).data('action');

  if (action == 'remove') {
    return "<span class=\'icon icon-checkbox\'>&nbsp;Shortlist</span><hr /><a href=\'/shortlist\'>View shortlist</a>";

  }
  return "<span class=\'icon icon-checkbox-unchecked\'>&nbsp;Shortlist</span><hr /><a href=\'/shortlist\'>View shortlist</a>";


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