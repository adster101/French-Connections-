var markers = [];


jQuery(document).ready(function () {

  twttr.ready(
          function (twttr) {
            twttr.conversion.trackPid('l526m');

          }
  );
  (function () {
    var _fbq = window._fbq || (window._fbq = []);
    if (!_fbq.loaded) {
      var fbds = document.createElement('script');
      fbds.async = true;
      fbds.src = '//connect.facebook.net/en_US/fbds.js';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(fbds, s);
      _fbq.loaded = true;
    }
    _fbq.push(['addPixelId', '528120040655478']);
  })();
  window._fbq = window._fbq || [];
  window._fbq.push(['track', 'PixelInitialized', {}]);

  // This is purely to accommodate A/B testing of an alternative search layout
  // This or the above would need to be removed once the 'experiment' is finished.
  jQuery('#map-search-tab a[data-toggle="tab"]').on('show.bs.tab', function (e) {

    jQuery(e.target).toggle();
    jQuery(e.relatedTarget).toggle();

    // Store the selected tab #ref in local storage, IE8+
    var selectedTab = jQuery(e.target).attr('href');


    // Set the local storage value so we 'remember' where the user was
    localStorage['selectedTab'] = selectedTab;

    if (selectedTab === '#mapsearch') {

      // Init google maps if not already init'ed
      if (!window.google) {
        loadGoogleMaps('initabsearchmap'); // Asych load the google maps stuff
      }

      var template = jQuery('#template').html();

      var data = [];

      jQuery('.search-result').each(function () {
        var tmp = jQuery(this).data();
        data = data.concat(tmp);
      });

      // Render the map search results
      Mustache.parse(template); // optional, speeds up future uses
      var rendered = Mustache.render(template, data);

      jQuery('#target').html(rendered);

      jQuery('.map-search-results .map-search-result').hover(
              function () {
                var index = jQuery('.map-search-result').index(this);
                markers[index].setAnimation(google.maps.Animation.BOUNCE);
              },
              function () {
                var index = jQuery('.map-search-result').index(this);
                markers[index].setAnimation(null);
              });
    }

    location.hash = "#property-search";

  });

  // Works on the tabs on the search results page. Needs to be made more generic
  jQuery('#search-tabs a[data-toggle="tab"]').on('show.bs.tab', function (e) {

    //jQuery('#map_canvas').hide();
    if (!window.google) {
      loadGoogleMaps('initmap'); // Asych load the google maps stuff
    }

    // Store the selected tab #ref in local storage, IE8+
    localStorage['selectedTab'] = jQuery(e.target).attr('href');
    // Get the selected tab from the local storage
    var selectedTab = localStorage['selectedTab'];
    var infowindow;
    // If the selected tab is the map tag then grab the markers
    if (selectedTab == '#mapsearch') {

      // The path of the search, e.g. /search or /fr/search
      // This must either be 'forsale' or 'accommodation'
      var action = jQuery('#property-search').attr('action').split('/');
      // Filter out the empty elements
      action = action.filter(function (e) {
        return e
      });
      var s_kwds = action[1];
      var option = action[0];
      var component = (option == 'forsale') ? 'com_realestatesearch' : 'com_fcsearch';
      var path = getPath();
      // Do an ajax call to get a list of towns...
      jQuery.getJSON("/index.php?option=" + component + "&task=mapsearch.markers&format=json", {
        s_kwds: path
      },
      function (data) {

        // Get the map instance
        map = document.map;
        markers = [];
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
          attachContent(marker, content, 300);
          markers[i] = marker;
          //  Create a new viewpoint bound, so we can centre the map based on the markers
          var bounds = new google.maps.LatLngBounds();
          //  Go through each...
          jQuery.each(markers, function (index, marker) {
            bounds.extend(marker.position);
          });
          //  Fit these bounds to the map
          map.fitBounds(bounds);
        }

        var markerCluster = new MarkerClusterer(map, markers, {
          maxZoom: 12,
          gridSize: 60,
          averageCenter: false
        });

      }).done(function () {

      });
    }

    jQuery('#map_canvas').show();
  });

  // Get the selected tab, if any 
  var selectedTab = localStorage['selectedTab'];

  // Default to show the list tab if nothing saved in localStorage
  if (typeof selectedTab !== 'undefined')
  {
    // and set the tab accordingly...
    jQuery('.nav li a[href="' + selectedTab + '"]').tab('show');
  } else {
    jQuery('.nav li a[href="#list"]').tab('show');
  }

  if (jQuery('.overthrow').length) {
    overthrow.sidescroller(document.querySelectorAll(".overthrow-enabled .sidescroll-nextprev"), {
      rewind: true,
      fixedItemWidth: true
    });
  }

  // Updates the form action based on the payment selection for @leisure booking.
  jQuery('.atleisure-booking-form input').on('change', function () {
    var el = jQuery(this);
    var action = el.attr('value');
    jQuery(".atleisure-booking-form").attr("action", action);
  });


  // Event tracking for enquiry form 
  jQuery('#rental-contact-form :input').not(':input[type=submit]').on('focus', function (event) {
    var target = jQuery(event.target);
    var input = '';

    if (target.is('select'))
    {
      input = jQuery(this).siblings('label').text();

    } else {
      input = jQuery(this).parent().parent().find('label').text();
    }
    ga('send', 'event', 'Enquiry Form', 'Rental property', input);

  });

  // Google analytics event tracking
  jQuery('#search-tabs li > a').on('click', function (e) {
    ga('send', 'event', 'Navigation', 'Search', e.target.hash);
  });

  // Google analytics event tracking
  jQuery('#main-nav li > a').on('click', function () {
    ga('send', 'event', 'Navigation', 'Main', jQuery(this).attr('href'));
  });

  jQuery('#enquiry').on('click', function () {
    ga('send', 'event', 'button', 'click', 'enquiry-button-clicked');
  });

  jQuery('.view-featured-fp-link').on('click', function () {
    ga('send', 'event', 'Featured property', 'Homepage', jQuery(this).attr('href'));
  });

  // Tracks click through from properties featured on search pages.
  jQuery('.view-search-fp-link').on('click', function () {
    ga('send', 'event', 'Featured property', 'Search pages', jQuery(this).attr('href'));
  });

  // Check whether placeholder is supported or not.
  if (document.createElement("input").placeholder == undefined) {
    // Placeholder is not supported, so remove the attribute
    jQuery('input').removeAttr('placeholder');
  }

  if (jQuery('.calendar').length) {
    jQuery('.calendar').datepicker({
      format: "dd-mm-yyyy",
      autoclose: true
    });
  }

  if (jQuery('.start_date.date').length) {
    // Set a temporary date object
    var nowTemp = new Date();

    // Get a date object in the correct format for the date picker
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    // Get the data from the DOM element
    var data = jQuery('.start_date.date').data();

    // Init the date picker on the start date field
    var start = jQuery('.start_date.date');

    start.datepicker({
      format: 'dd-mm-yyyy',
      daysOfWeekHighlighted: data.highlight,
      daysOfWeekDisabled: data.changeover,
      startDate: now,
      autoclose: true
    })

    // Init the date picker on the end date field
    var end = jQuery('.end_date.date');

    end.datepicker({
      format: 'dd-mm-yyyy',
      daysOfWeekHighlighted: data.highlight,
      daysOfWeekDisabled: data.changeover,
      startDate: now,
      autoclose: true
    })

    // When the start date changes update the startDate for the departure date calendar
    start.on('changeDate', function (ev) {

      // Get the start (arrival) date
      var date = new Date(ev.date);

      // If the calendar is set to highlight days add seven days 
      // Assumes that this property is highlighting one day and that booking period
      // is for a seven night stay
      if (data.highlight) {
        date.setDate(date.getDate() + 7);
      } else {
        date.setDate(date.getDate() + 1);
      }

      // setStartDate
      end.datepicker('setStartDate', date);

      // Update the calendar object
      end.datepicker('update', date);
    })

  }
  // Load the google maps crap, only if there is a #map on the page.
  // Use #map generically and #location_map for property specific pages etc
  if (jQuery('#map').length) {
    loadGoogleMaps('initialise');
  }

  jQuery('.result-links a.login').tooltip({
    animation: false
  });

  if (jQuery('#newUnit').length) {
    jQuery('#newUnit').on('click', function (event) {
      if (!confirm(Joomla.JText._('COM_RENTAL_LISTING_CONFIRM_ADDITIONAL_UNIT'))) {
        event.preventDefault();
      }
    })
  }

  // TO DO - What a fucking mess!
  jQuery('.shortlist').each(function () { // For each result
    // Get the data-action state
    jQuery(this).popover({// Initialise a popover
      trigger: 'manual' // Take control of when the popover is opened
    }).click(function (event) {
      event.preventDefault(); // Prevent the default click behaviour
      jQuery('.shortlist').not(this).popover('hide'); // Hide any other popovers that are open
      jQuery(this).popover('toggle'); // Manually open the popover 
    })
  })

  // TO DO - What a fucking mess!
  jQuery('body').on('change', '.popover input ', function (ev) { // When a pop over span is clicked
    var el = jQuery(this);
    // favourite is the anchor element that triggers a popover
    var favourite = jQuery('.popover').siblings('a.shortlist');
    var dataObj = favourite.data(); // Get the data attributes of the parent a element
    var url_params = {};
    var userToken = dataObj.token;
    url_params.id = dataObj.id;
    url_params.action = dataObj.action;

    var url = '/index.php?option=com_shortlist&task=shortlist.update&tmpl=component&' + userToken + '=1';
    jQuery.ajax({
      dateType: "json",
      url: url,
      data: url_params
    }).done(function (data) {

      var popover = jQuery('.popover').data('bs.popover');

      if (data == 1) {
        dataObj.action = (dataObj.action === 'add') ? 'remove' : 'add'; // action is the state the object is changing *to* not what what it is now...

        if (dataObj.action == 'remove') {
          favourite.addClass('in-shortlist');
          favourite.attr('data-state', true);
          favourite.attr('data-content', '<ul class=\'nav nav-pills nav-stacked\'><li><div class=\'checkbox\'><label><input type=\'checkbox\' checked value=\'1\'> My Shortlist</input></label></div></li><li class=\'divider\'><hr /></li><li><a href=\'/my-account/shortlist\'>View shortlist</a></li></ul>');
        } else {
          favourite.removeClass('in-shortlist');
          favourite.attr('data-state', false);
          favourite.attr('data-content', '<ul class=\'nav nav-pills nav-stacked\'><li><div class=\'checkbox\'><label><input type=\'checkbox\' value=\'0\'> My Shortlist</input></label></div></li><li class=\'divider\'><hr /></li><li><a href=\'/my-account/shortlist\'>View shortlist</a></li></ul>');
        }

        // If action is remove then add icon-checkbox else remove it
        favourite.attr('data-action', dataObj.action);

      } else {

        popover.options.content = '<p>Session expired.<br /> Please login.</p>';
      }
    })
  });

  // TO DO -make the below into a encapsulated function and reduce code here
  // e.g. the show_contact method below is too similar to this functionality 
  // so should be made generic (and reusable)
  var use_invoice = jQuery('#jform_use_invoice_address');
  if (use_invoice.length)
  {

    use_invoice.attr('checked', false);

    jQuery("#jform_use_invoice_address").on('change', function (e) {

      var siblings = jQuery(this).parent().parent().siblings('div');
      var inputs = siblings.find(':input');
      var checked = jQuery(this).is(':checked');

      // Loop over each input and activate/deactive the required attr
      inputs.each(function () {
        field = jQuery(this);

        if (!checked) {
          field.addClass('required');
          field.attr('required', 'required');
        } else {
          field.removeClass('required');
          field.removeAttr('required');
        }
      });

      // Just show/hide all the form fields as appropriate
      siblings.toggle();
    })
  }

  var country_select = jQuery('select#jform_BillingCountry');

  if (country_select.length)
  {
    country_select.on('change', function (e)
    {
      var select = jQuery(this);
      var value = select.val();
      var state = jQuery('select#jform_BillingState');

      if (value === 'US')
      {
        state.addClass('required');
        state.attr('required', 'required');
        state.parent().parent().show();
      } else {
        state.removeClass('required');
        state.removeAttr('required');
        state.parent().parent().hide();
      }
    })
  }

  var country_select = jQuery('select#jform_country');

  if (country_select.length)
  {
    country_select.on('change', function (e)
    {
      var select = jQuery(this);
      var value = select.val();
      var state = jQuery('select#jform_state');

      if (value === 'US')
      {
        state.addClass('required');
        state.attr('required', 'required');
        state.parent().parent().show();
      } else {
        state.removeClass('required');
        state.removeAttr('required');
        state.parent().parent().hide();
      }
    })
  }

  if (jQuery("#contactDetails").length) {

    var checked = jQuery('#jform_use_invoice_details');

    show_contact(checked);

    jQuery("#jform_use_invoice_details").on('change', function (e) {

      show_contact(this);

    })
  }

  if (jQuery("#jform_vat_status").length) {
    // Init the VAT field if it's found on the page. In an ideal world this would only be loaded on the pages that need it, via require.js
    var vatID = jQuery("#jform_vat_status")[0].value;
    show_vat(vatID);

    jQuery("#jform_vat_status").change(function (e) {
      // Get the selected vat_aid_id code
      var vatID = jQuery(this)[0].value;
      show_vat(vatID);
    });

  }

  // Bind a change function to all forms that need validation.
  // Gives an alert if unsaved changes will be lost.
  jQuery('form.form-validate').change(function () {
    //window.onbeforeunload = function() {
    //return Joomla.JText._('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    //};
  });

  try {
    // If the tinymce editor is loaded
    if (window.tinymce) {
      // Bind the addEditor event to editor(s)
      tinymce.on('addEditor', function (editor) {
        // Bind the blur event to editor(s)
        tinyMCE.activeEditor.on('blur', function (e) {
          // Check whether the actie editor 'is dirty'
          if (tinyMCE.activeEditor.isDirty()) {
            // If so, do the business on before unload
            window.onbeforeunload = function () {
              return Joomla.JText._('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
            };
          }
        });
      });
    }
  } catch (e) {
    // what to do!?
  }

  // Add special offer counter... 
  jQuery('#jform_offer_description').each(function () {

    // Assign this to that so we can use this later...
    var that = this;

    // Update the span element with the initial value of the caption
    var length = jQuery('#jform_offer_description').val().length;
    jQuery('span.offer-counter').text(150 - length);

    // Add the maxlength attribute
    jQuery(this).attr('maxlength', 150);

    jQuery(this).on('keyup', function (event) {

      // On the keyup event, update the value of the span count element
      var length = jQuery(this).val().length;

      jQuery('.offer-counter').text((150 - length));

    });
  });

  // Figure out what to do with local storage
  // Update tab-href dependent on window.location.hash
  // localStorage.removeItem('tab-href');

  // Javascript to enable link to tab
  var hash = document.location.hash;
  var prefix = 'tab-';
  if (hash) {
    jQuery('.nav-tabs a[href=' + hash.replace(prefix, '') + ']').tab('show');
    localStorage.setItem('tab-href', window.location.hash);
  }

  // Change hash for page-reload
  jQuery('.nav-tabs a').on('shown', function (e) {
    window.location.hash = e.target.hash.replace("#", "#" + prefix);
  });
});

var infowindow;

var RecaptchaOptions = {
  theme: "custom",
  custom_theme_widget: "recaptcha_widget"
}

// The five markers show a secret message when clicked
// but that message is not within the marker's instance data
function attachContent(marker, num, width) {
  google.maps.event.addListener(marker, 'click', function () {

    if (infowindow)
      infowindow.close();

    infowindow = new google.maps.InfoWindow({
      content: num,
      maxWidth: width
    });
    infowindow.open(marker.get('map'), marker);
  });
}

var loadGoogleMaps = function (func) {

  if (typeof google === 'object' && typeof google.maps === 'object') {
    window[func];
  } else {
    var script = document.createElement('script');
    script.type = 'text/javascript';

    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBudTxPamz_W_Ou72m2Q8onEh10k_yCwYI&sensor=true&' +
            'callback=' + func;
    document.body.appendChild(script);
  }
}

function initialise() {
  var data = jQuery('#map').data();
  var lat = data.lat;
  var lon = data.lon;
  var myLatLng = new google.maps.LatLng(lat, lon);
  var myOptions = {
    center: myLatLng,
    zoom: 6,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    disableDefaultUI: false,
    zoomControl: true
  };

  var map = new google.maps.Map(document.getElementById("map"), myOptions);
  var marker = new google.maps.Marker({
    position: myLatLng,
    map: map
  });
  google.maps.event.addListener(map, 'zoom_changed', function () {
    // 3 seconds after the center of the map has changed, pan back to the
    // marker.
    window.setTimeout(function () {
      map.panTo(marker.getPosition());
    }, 1500);
  });
}


function initabsearchmap() {


  // Move this to CSS file
  jQuery('#map_canvas').css('width', '100%');
  jQuery('#map_canvas').css('height', '600px');

  var myLatLng = new google.maps.LatLng(46.8, 2.8);
  var myOptions = {
    center: myLatLng,
    zoom: 7,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    disableDefaultUI: true,
    zoomControl: true
  }

  var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);


  jQuery('.search-result').each(function (i) {
    var data = jQuery(this).data();

    // The lat long of the propert, units will appear stacked on top...
    var myLatlng = new google.maps.LatLng(data.latitude, data.longitude);
    // Create the marker instance
    marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      icon: '/images/mapicons/iconflower.png'
    });
    marker.setTitle((data.unitTitle).toString());
    content = '<h4><a href="' + data.url + '">' +
            data.unitTitle + '</a></h4><div class="media"><a class="pull-left" href="' +
            data.url + '"><img class="media-object" src="' +
            data.thumbnail + '"/></a><div class="media-body"><p>' +
            data.tagline + '</p></div><p><a class="btn btn-primary" href="' + data.url + '">asds</a></div>';

    attachContent(marker, content, 360);

    markers.push(marker);

    //  Create a new viewpoint bound, so we can centre the map based on the markers
    var bounds = new google.maps.LatLngBounds();

    //  Go through each...
    jQuery.each(markers, function (index, marker) {
      bounds.extend(marker.position);
    });

    //  Fit these bounds to the map
    map.fitBounds(bounds);



  });


}

/* define some useful functions, innit! */
var show_vat = function (vatID) {
  vat_number = jQuery('#vat_number');
  company_number = jQuery('#company_number');

  // If vatID is za then show the siret number field and add relevant hidden fields to make this a required field ( on server side ).
  if (vatID === 'ZA') {

    // Display the Company number field
    company_number.show();

    // Need to remove any hidden fields from VATNUMBER field that make it required.
    vat_number.hide();
    toggle('#jform_vat_number', false);
    toggle('#jform_company_number', true);

  } else if (vatID === 'ECS') {

    // Add hidden input field for company number 
    vat_number.show();
    toggle('#jform_vat_number', true);
    toggle('#jform_company_number', false);

    company_number.hide();

  } else {
    // Hide the non required fields and toggle the validation attributed
    company_number.hide();
    vat_number.hide();
    toggle('#jform_vat_number', false);
    toggle('#jform_company_number', false);
  }
};

/* 
 * Simple function which adds or removed the required class and toggles the required attribute
 */
var toggle = function (elem, show) {

  field = jQuery(elem);
  if (show) {
    field.addClass('required');
    field.attr('required', 'required');
  } else {
    field.removeClass('required');
    field.removeAttr('required');
  }
}

var show_contact = function (that, selector) {

  if (jQuery(that).is(':checked')) {
    jQuery("#contactDetails").hide();

    // Loop over and deactivate all form fields.
    jQuery('#contactDetails').find('input[type=text]').each(function () {
      toggle(this, false);
    })
  } else {
    jQuery("#contactDetails").show();
    jQuery('#contactDetails').find('input[type=text]').each(function () {
      toggle(this, true);
    })
  }
}


/* Fires on occasion when a button has it bound to it's onclick event */
Joomla.submitbutton = function (task)
{
  if (task == '')
  {
    return false;
  }
  else
  {
    var isValid = true;
    var action = task.split('.');

    if (action[1] != 'cancel' && action[1] != 'close')
    {
      var forms = jQuery('form.form-validate');
      for (var i = 0; i < forms.length; i++)
      {
        if (!document.formvalidator.isValid(forms[i]))
        {
          isValid = false;
          break;
        }
      }
    }

    if (isValid)
    {
      // Unbind the onbeforeunload event
      window.onbeforeunload = null;

      if (action[1] == 'apply')
      {
        jQuery('#toolbar-apply > button').button('loading');
        jQuery('#actions-apply > button').button('loading');
      }
      if (action[1] == 'save')
      {
        jQuery('#toolbar-save > button').button('loading');
        jQuery('#actions-save > button').button('loading');
      }

      if (action[0] == 'payment') {
        jQuery('.payment-button').button('loading');
      }

      Joomla.submitform(task);
      return true;
    }
    else
    {
      alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED', ''));
      return false;
    }
  }
};

jQuery(function () {
  var activeTab = jQuery('a[href="' + location.hash + '"]');
  activeTab && activeTab.tab('show');
});

window.twttr = (function (d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0],
          t = window.twttr || {};
  if (d.getElementById(id))
    return t;
  js = d.createElement(s);
  js.id = id;
  js.src = "https://platform.twitter.com/oct.js";
  fjs.parentNode.insertBefore(js, fjs);

  t._e = [];
  t.ready = function (f) {
    t._e.push(f);
  };

  return t;
}(document, "script", "twitter-wjs"));