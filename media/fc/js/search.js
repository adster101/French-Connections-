var infowindow;
jQuery(document).ready(function () {

    jQuery('[data-toggle="popover"]').popover()


  jQuery('.lastminute-date-search-link').each(function () {

    jQuery(this).on('click', function (event) {

      var data = jQuery(this).data();
      var start = data.start;
      var end = data.end;
      jQuery('#arrival').attr('value', start);
      jQuery('#departure').attr('value', end);
      var path = getPath();
      // Amend the path that the form is submitted to
      jQuery('form#property-search').attr('action', path);
      // Submit the form
      jQuery('form#property-search').submit();
      event.preventDefault();
    });
  });

  jQuery('.property-search-button').on('click', function (event) {

    event.preventDefault();
    var path = getPath();
    // Amend the path that the form is submitted to
    jQuery('form#property-search').attr('action', path);
    // Submit the form
    jQuery('form#property-search').submit();
  });
  jQuery('#sort_by').on('change', function (event) {

    event.preventDefault();
    var path = getPath();
    // Amend the path that the form is submitted to
    jQuery('form#property-search').attr('action', path);
    // Submit the form
    jQuery('form#property-search').submit();
  });
  // Bind the typeahead business
  jQuery(".typeahead").typeahead({
    source: function (query, process) {
      jQuery.get('/index.php?option=com_fcsearch&task=suggestions.display&format=json&tmpl=component',
              {
                q: query,
                items: 25
              },
      function (data) {
        process(data);
      }
      )
    },
    items: 25
  })


  // Deal with the more/less options for the refine search bit.
  jQuery("a.show").click(function (event) {

    // Prevent the default click behaviour
    event.preventDefault();

    // Get the containing element that we want to show/hide
    jQuery(this).prev().prev().toggleClass('show');

    // Check the open/closed state
    jQuery(this).html(function (i, v) {
      return v.trim() === Joomla.JText._('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS')
              ? Joomla.JText._('COM_FCSEARCH_SEARCH_SHOW_LESS_OPTIONS')
              : Joomla.JText._('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS');
    });
  })

}) // End of on DOM ready

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

  // get the current pathway as an array
  var pathArray = window.location.pathname.split('/');
  // The path of the search, e.g. /search or /fr/search
  // This must either be 'forsale' or 'accommodation'
  var action = jQuery('#property-search').attr('action').split('/');
  var path = '/' + action[1];
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
  // Loop over the path aray
  for (i = 0; i < pathArray.length; i++) {

    if (pathArray[i].indexOf('property_') >= 0) {
      path = path + '/' + [pathArray[i]];
    }
    if (pathArray[i].indexOf('accommodation_') >= 0) {
      path = path + '/' + [pathArray[i]];
    }
    if (pathArray[i].indexOf('external') >= 0) {
      path = path + '/' + [pathArray[i]];
    }
    if (pathArray[i].indexOf('internal_') >= 0) {
      path = path + '/' + [pathArray[i]];
    }
    if (pathArray[i].indexOf('suitability_') >= 0) {
      path = path + '/' + [pathArray[i]];
    }

  }

  if (arrival !== '' && typeof (arrival) !== 'undefined') {
    path = path + '/arrival_' + arrival;
  }

  if (departure !== '' && typeof (departure) !== 'undefined') {
    path = path + '/departure_' + departure;
  }
  if (occupancy !== '' && typeof (occupancy) !== 'undefined') {
    path = path + '/occupancy_' + occupancy;
  }

  if (bedrooms !== '' && typeof (bedrooms) !== 'undefined') {
    path = path + '/bedrooms_' + bedrooms;
  }

  if (sort_by !== '' && typeof (sort_by) !== 'undefined') {
    path = path + '/' + sort_by;
  }

  if (min_price !== '' && typeof (min_price) !== 'undefined') {
    path = path + '/' + min_price;
  }

  if (max_price !== '' && typeof (max_price) !== 'undefined') {
    path = path + '/' + max_price;
  }

  // Pull out the offers and LWL flags...
  var offers = loadPageVar("offers");
  var lwl = loadPageVar("lwl");

  // Fairly obvious but if we have both add to the string otherwise just add one or the other.
  if (offers === 'true' & lwl === 'true') {
    path = path + '?offers=true&lwl=true';
  } else if (offers === 'true') {
    path = path + '?offers=true';
  } else if (lwl === 'true') {
    path = path + '?lwl=true';
  }

  return path;
}

function initmap() {

  jQuery('#map_canvas').css('width', '100%');
  jQuery('#map_canvas').css('height', '800px');
  var myLatLng = new google.maps.LatLng(46.8, 2.8);
  var myOptions = {
    center: myLatLng,
    zoom: 7,
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
  google.maps.event.addListener(marker, 'click', function () {

    if (infowindow)
      infowindow.close();
    infowindow = new google.maps.InfoWindow({
      content: num,
      maxWidth: 300
    });
    infowindow.open(marker.get('map'), marker);
  });
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
    String.prototype.trim = function () {
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

// Pulls out the value of the query paramter sVar
function loadPageVar(sVar) {
  return decodeURI(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURI(sVar).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}
