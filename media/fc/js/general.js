jQuery(document).ready(function() {

  // Check whether placeholder is supported or not.
  if (document.createElement("input").placeholder == undefined) {
    // Placeholder is not supported, so remove the attribute
    jQuery('input').removeAttr('placeholder');
  }

  var nowTemp = new Date();
  var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

  var checkin = $('.start_date.date').datepicker({
    format: "dd-mm-yyyy",
    beforeShowDay: function(date) {
      return date.valueOf() >= now.valueOf();
    },
    autoclose: true

  }).on('changeDate', function(ev) {
    if (ev.date.valueOf() > checkout.datepicker("getDate").valueOf() || !checkout.datepicker("getDate").valueOf()) {

      var newDate = new Date(ev.date);
      newDate.setDate(newDate.getDate() + 1);
      checkout.datepicker("update", newDate);

    }
    $('.end_date input')[0].focus();
  });


  var checkout = $('.end_date.date').datepicker({
    format: "dd-mm-yyyy",
    beforeShowDay: function(date) {
      if (!checkin.datepicker("getDate").valueOf()) {
        return date.valueOf() >= new Date().valueOf();
      } else {
        return date.valueOf() > checkin.datepicker("getDate").valueOf();
      }
    },
    autoclose: true

  }).on('changeDate', function(ev) {
  });

  // Load the google maps crap, only if there is a #map on the page.
  // Use #map generically and #location_map for property specific pages etc
  if (jQuery('#map').length) {
    loadGoogleMaps('initialise');
  }

  jQuery('.result-links a.login').tooltip({animation: false});



  if (jQuery('#newUnit').length) {
    jQuery('#newUnit').on('click', function(event) {
      if (!confirm(Joomla.JText._('COM_RENTAL_LISTING_CONFIRM_ADDITIONAL_UNIT'))) {
        event.preventDefault();
      }
    })
  }

  jQuery('.shortlist').each(function() { // For each result

    // Get the data-action state
    jQuery(this).popover({// Initialise a popover
      trigger: 'manual' // Take control of when the popover is opened
    }).click(function(event) {
      event.preventDefault(); // Prevent the default click behaviour
      jQuery('.shortlist').not(this).popover('hide'); // Hide any other popovers that are open
      console.log(jQuery(this).data());
      popover = jQuery(this).data('bs.popover'); // Get the popover data attributes
      popover.options.html = true; // Update the content by calling getContent
      popover.options.content = getContent(this); // Update the content by calling getContent
      jQuery(this).popover('toggle'); // Manually open the popover 
    });
  })

  jQuery('body').on('click', '.popover span', function(ev) { // When a pop over span is clicked
    var el = jQuery(this);
    var favourite = el.parent().parent().siblings('a.shortlist');
    var dataObj = favourite.data(); // Get the data attributes of the parent a element
    var url_params = {};
    var userToken = document.getElementsByTagName("input")[0].name;
    url_params.id = dataObj.id;
    url_params.action = dataObj.action;


    var url = '/index.php?option=com_shortlist&task=shortlist.update&tmpl=component&' + userToken + '=1';
    jQuery.ajax({
      dateType: "json",
      url: url,
      data: url_params
    }).done(function(data) {

      if (data == 1) {
        dataObj.action = (dataObj.action === 'add') ? 'remove' : 'add'; // action is the state the object is changing *to* not what what it is now...
        favourite.data(dataObj);

        if (dataObj.action == 'remove') {
          el.addClass('glyphicon-ok');
          el.removeClass('glyphicon-remove');
        } else {
          el.addClass('glyphicon-remove');
          el.removeClass('glyphicon-ok');
        } // If action is remove then add icon-checkbox else remove it
        (dataObj.action == 'remove') ? favourite.toggleClass('muted', false) : favourite.toggleClass('muted', true); // If action is remove then add icon-checkbox else remove it
        favourite.attr('data-action', dataObj.action);

      } else {
        jQuery('.shortlist').addClass('muted');
        el.removeClass('glyphicon-ok glyphicon-remove').html('<p>Session expired.<br /> Please login.</p>');
      }
    })
  });


  if (jQuery('.start_date').length) {

    var start_date = jQuery('.start_date').attr('value');

    if (start_date == '') {
      start_date = new Date();
    }

    jQuery('.start_date').datepicker({
      numberOfMonths: 1,
      showOn: "both",
      dateFormat: "dd-mm-yy",
      buttonImageOnly: true,
      buttonImage: "/media/system/images/calendar.png",
      showButtonPanel: true,
      onSelect: function(selectedDate) {
        jQuery('.end_date').datepicker("option", "minDate", selectedDate);
      }
    });

    jQuery('.end_date').datepicker({
      numberOfMonths: 1,
      dateFormat: "dd-mm-yy",
      showOn: "both",
      buttonImageOnly: true,
      buttonImage: "/media/system/images/calendar.png",
      minDate: start_date,
      showButtonPanel: true
    });
  }
  ;



  if (jQuery("#contactDetails").length) {

    var checked = jQuery('#jform_use_invoice_details');

    show_contact(checked);

    jQuery("#jform_use_invoice_details").on('change', function(e) {

      show_contact(this);

    })
  }

  if (jQuery("#jform_vat_status").length) {
    // Init the VAT field if it's found on the page. In an ideal world this would only be loaded on the pages that need it, via require.js
    var vatID = jQuery("#jform_vat_status")[0].value;
    show_vat(vatID);

    jQuery("#jform_vat_status").change(function(e) {
      // Get the selected vat_aid_id code
      var vatID = jQuery(this)[0].value;
      show_vat(vatID);
    });

  }

  // Bind a change function to all forms that need validation.
  // Gives an alert if unsaved changes will be lost.
  jQuery('form.form-validate').change(function() {
    window.onbeforeunload = function() {
      return Joomla.JText._('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    };
  });

  try {
    // If the tinymce editor is loaded
    if (window.tinymce) {
      // Bind the addEditor event to editor(s)
      tinymce.on('addEditor', function(editor) {
        // Bind the blur event to editor(s)
        tinyMCE.activeEditor.on('blur', function(e) {
          // Check whether the actie editor 'is dirty'
          if (tinyMCE.activeEditor.isDirty()) {
            // If so, do the business on before unload
            window.onbeforeunload = function() {
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
  jQuery('#jform_offer_description').each(function() {


    // Assign this to that so we can use this later...
    var that = this;

    // Update the span element with the initial value of the caption
    var length = jQuery('#jform_offer_description').val().length;
    jQuery('span.offer-counter').text(150 - length);

    // Add the maxlength attribute
    jQuery(this).attr('maxlength', 150);

    jQuery(this).on('keyup', function(event) {

      // On the keyup event, update the value of the span count element
      var length = jQuery('#jform_offer_description').val().length;

      jQuery('.offer-counter').text(150 - length);

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
  jQuery('.nav-tabs a').on('shown', function(e) {
    window.location.hash = e.target.hash.replace("#", "#" + prefix);
  })
});

var infowindow;

// The five markers show a secret message when clicked
// but that message is not within the marker's instance data
function attachContent(marker, num, width) {
  google.maps.event.addListener(marker, 'click', function() {

    if (infowindow)
      infowindow.close();

    infowindow = new google.maps.InfoWindow({
      content: num,
      maxWidth: width
    });
    infowindow.open(marker.get('map'), marker);
  });
}

var loadGoogleMaps = function(func) {

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
  google.maps.event.addListener(map, 'zoom_changed', function() {
    // 3 seconds after the center of the map has changed, pan back to the
    // marker.
    window.setTimeout(function() {
      map.panTo(marker.getPosition());
    }, 1500);
  });
}

var getContent = function(that) {

  action = jQuery(that).data('action');

  if (action == 'remove') {
    return "<span class='click glyphicon glyphicon-remove'> Shortlist</span><hr /><a href='/shortlist'>View shortlist</a>";

  }
  return "<span class='click glyphicon glyphicon-ok'> Shortlist</span><hr /><a href='/shortlist'>View shortlist</a>";


}

/* define some useful functions, innit! */
var show_vat = function(vatID) {
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
var toggle = function(elem, show) {

  field = jQuery(elem);
  if (show) {
    field.addClass('required');
    field.attr('required', 'required');
  } else {
    field.removeClass('required');
    field.removeAttr('required');
  }
}

var show_contact = function(that) {


  if (jQuery(that).is(':checked')) {
    jQuery("#contactDetails").hide();

    // Loop over and deactivate all form fields.
    jQuery('#contactDetails').find('input[type=text]').each(function() {
      toggle(this, false);
    })
  } else {
    jQuery("#contactDetails").show();
    jQuery('#contactDetails').find('input[type=text]').each(function() {
      toggle(this, true);
    })
  }
}


/* Fires on occasion when a button has it bound to it's onclick event */
Joomla.submitbutton = function(task)
{
  if (task == '')
  {
    alert("woot");
    return false;
  }
  else
  {
    var isValid = true;
    var action = task.split('.');

    if (action[1] != 'cancel' && action[1] != 'close')
    {
      var forms = $$('form.form-validate');
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
      Joomla.submitform(task);
      return true;
    }
    else
    {
      alert(Joomla.JText._('COM_RENTAL_RENTAL_ERROR_UNACCEPTABLE', ''));
      return false;
    }
  }
}

jQuery(function() {
  var activeTab = jQuery('a[href="' + location.hash + '"]');
  activeTab && activeTab.tab('show');
});

  