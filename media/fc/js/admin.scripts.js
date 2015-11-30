Joomla=window.Joomla||{},Joomla.editors=Joomla.editors||{},Joomla.editors.instances=Joomla.editors.instances||{},function(e,t){"use strict";e.submitform=function(e,n,r){n||(n=t.getElementById("adminForm")),e&&(n.task.value=e),n.noValidate=!r;var i=t.createElement("input");i.style.display="none",i.type="submit",n.appendChild(i).click(),n.removeChild(i)},e.submitbutton=function(t){e.submitform(t)},e.JText={strings:{},_:function(e,t){return typeof this.strings[e.toUpperCase()]!="undefined"?this.strings[e.toUpperCase()]:t},load:function(e){for(var t in e){if(!e.hasOwnProperty(t))continue;this.strings[t.toUpperCase()]=e[t]}return this}},e.replaceTokens=function(e){if(!/^[0-9A-F]{32}$/i.test(e))return;var n=t.getElementsByTagName("input"),r,i,s;for(r=0,s=n.length;r<s;r++)i=n[r],i.type=="hidden"&&i.value=="1"&&i.name.length==32&&(i.name=e)},e.isEmail=function(e){var t=/^[\w.!#$%&‚Äô*+\/=?^`{|}~-]+@[a-z0-9-]+(?:\.[a-z0-9-]{2,})+$/i;return t.test(e)},e.checkAll=function(e,t){if(!e.form)return!1;t=t?t:"cb";var n=0,r,i,s;for(r=0,s=e.form.elements.length;r<s;r++)i=e.form.elements[r],i.type==e.type&&i.id.indexOf(t)===0&&(i.checked=e.checked,n+=i.checked?1:0);return e.form.boxchecked&&(e.form.boxchecked.value=n),!0},e.renderMessages=function(n){e.removeMessages();var r=t.getElementById("system-message-container"),i,s,o,u,a,f,l;for(i in n){if(!n.hasOwnProperty(i))continue;s=n[i],o=t.createElement("div"),o.className="alert alert-"+i,u=e.JText._(i),typeof u!="undefined"&&(a=t.createElement("h4"),a.className="alert-heading",a.innerHTML=e.JText._(i),o.appendChild(a));for(f=s.length-1;f>=0;f--)l=t.createElement("p"),l.innerHTML=s[f],o.appendChild(l);r.appendChild(o)}},e.removeMessages=function(){var e=t.getElementById("system-message-container");while(e.firstChild)e.removeChild(e.firstChild);e.style.display="none",e.offsetHeight,e.style.display=""},e.isChecked=function(e,n){typeof n=="undefined"&&(n=t.getElementById("adminForm")),n.boxchecked.value+=e?1:-1;if(!n.elements["checkall-toggle"])return;var r=!0,i,s,o;for(i=0,o=n.elements.length;i<o;i++){s=n.elements[i];if(s.type=="checkbox"&&s.name!="checkall-toggle"&&!s.checked){r=!1;break}}n.elements["checkall-toggle"].checked=r},e.popupWindow=function(e,t,n,r,i){var s=(screen.width-n)/2,o=(screen.height-r)/2,u="height="+r+",width="+n+",top="+o+",left="+s+",scrollbars="+i+",resizable";window.open(e,t,u).window.focus()},e.tableOrdering=function(n,r,i,s){typeof s=="undefined"&&(s=t.getElementById("adminForm")),s.filter_order.value=n,s.filter_order_Dir.value=r,e.submitform(i,s)},window.writeDynaList=function(e,n,r,i,s){var o="<select "+e+">",u=r==i,a=0,f,l,c;for(l in n){if(!n.hasOwnProperty(l))continue;c=n[l];if(c[0]!=r)continue;f="";if(u&&s==c[1]||!u&&a===0)f='selected="selected"';o+='<option value="'+c[1]+'" '+f+">"+c[2]+"</option>",a++}o+="</select>",t.writeln(o)},window.changeDynaList=function(e,n,r,i,s){var o=t.adminForm[e],u=r==i,a,f,l,c;while(o.firstChild)o.removeChild(o.firstChild);a=0;for(f in n){if(!n.hasOwnProperty(f))continue;l=n[f];if(l[0]!=r)continue;c=new Option,c.value=l[1],c.text=l[2];if(u&&s==c.value||!u&&a===0)c.selected=!0;o.options[a++]=c}o.length=a},window.radioGetCheckedValue=function(e){if(!e)return"";var t=e.length,n;if(t===undefined)return e.checked?e.value:"";for(n=0;n<t;n++)if(e[n].checked)return e[n].value;return""},window.getSelectedValue=function(e,n){var r=t[e][n],i=r.selectedIndex;return i!==null&&i>-1?r.options[i].value:null},window.listItemTask=function(e,n){var r=t.adminForm,i=0,s,o=r[e];if(!o)return!1;for(;;){s=r["cb"+i];if(!s)break;s.checked=!1,i++}return o.checked=!0,r.boxchecked.value=1,window.submitform(n),!1},window.submitbutton=function(t){e.submitbutton(t)},window.submitform=function(t){e.submitform(t)},window.saveorder=function(e,t){window.checkAll_button(e,t)},window.checkAll_button=function(n,r){r=r?r:"saveorder";var i,s;for(i=0;i<=n;i++){s=t.adminForm["cb"+i];if(!s){alert("You cannot change the order of items, as an item in the list is `Checked Out`");return}s.checked=!0}e.submitform(r)}}(Joomla,document);
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



  // Also need to integrate map here.
  jQuery('#map-search-tab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

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


  // Get the selected tab, if any and set the tab accordingly...
  var selectedTab = localStorage['selectedTab']; 
  console.log(selectedTab);

    jQuery('.nav li a[href="' + selectedTab + '"]').tab('show');
  


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
    console.log(input);
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

  try {

    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    var checkin = jQuery('.start_date.date').datepicker({
      format: "dd-mm-yyyy",
      beforeShowDay: function (date) {
        return date.valueOf() >= now.valueOf();
      },
      autoclose: true

    }).on('changeDate', function (ev) {
      if (ev.date.valueOf() > checkout.datepicker("getDate").valueOf() || !checkout.datepicker("getDate").valueOf()) {

        var newDate = new Date(ev.date);
        newDate.setDate(newDate.getDate() + 1);
        checkout.datepicker("update", newDate);

      }
      jQuery('.end_date input')[0].focus();
    });


    var checkout = jQuery('.end_date.date').datepicker({
      format: "dd-mm-yyyy",
      beforeShowDay: function (date) {
        if (!checkin.datepicker("getDate").valueOf()) {
          return date.valueOf() >= new Date().valueOf();
        } else {
          return date.valueOf() > checkin.datepicker("getDate").valueOf();
        }
      },
      autoclose: true

    }).on('changeDate', function (ev) {
    });

  } catch (e) {
    // what to do!?
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
/*! jQuery UI - v1.8.23 - 2012-08-15
* https://github.com/jquery/jquery-ui
* Includes: jquery.ui.core.js
* Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function(a,b){function c(b,c){var e=b.nodeName.toLowerCase();if("area"===e){var f=b.parentNode,g=f.name,h;return!b.href||!g||f.nodeName.toLowerCase()!=="map"?!1:(h=a("img[usemap=#"+g+"]")[0],!!h&&d(h))}return(/input|select|textarea|button|object/.test(e)?!b.disabled:"a"==e?b.href||c:c)&&d(b)}function d(b){return!a(b).parents().andSelf().filter(function(){return a.curCSS(this,"visibility")==="hidden"||a.expr.filters.hidden(this)}).length}a.ui=a.ui||{};if(a.ui.version)return;a.extend(a.ui,{version:"1.8.23",keyCode:{ALT:18,BACKSPACE:8,CAPS_LOCK:20,COMMA:188,COMMAND:91,COMMAND_LEFT:91,COMMAND_RIGHT:93,CONTROL:17,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,INSERT:45,LEFT:37,MENU:93,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SHIFT:16,SPACE:32,TAB:9,UP:38,WINDOWS:91}}),a.fn.extend({propAttr:a.fn.prop||a.fn.attr,_focus:a.fn.focus,focus:function(b,c){return typeof b=="number"?this.each(function(){var d=this;setTimeout(function(){a(d).focus(),c&&c.call(d)},b)}):this._focus.apply(this,arguments)},scrollParent:function(){var b;return a.browser.msie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?b=this.parents().filter(function(){return/(relative|absolute|fixed)/.test(a.curCSS(this,"position",1))&&/(auto|scroll)/.test(a.curCSS(this,"overflow",1)+a.curCSS(this,"overflow-y",1)+a.curCSS(this,"overflow-x",1))}).eq(0):b=this.parents().filter(function(){return/(auto|scroll)/.test(a.curCSS(this,"overflow",1)+a.curCSS(this,"overflow-y",1)+a.curCSS(this,"overflow-x",1))}).eq(0),/fixed/.test(this.css("position"))||!b.length?a(document):b},zIndex:function(c){if(c!==b)return this.css("zIndex",c);if(this.length){var d=a(this[0]),e,f;while(d.length&&d[0]!==document){e=d.css("position");if(e==="absolute"||e==="relative"||e==="fixed"){f=parseInt(d.css("zIndex"),10);if(!isNaN(f)&&f!==0)return f}d=d.parent()}}return 0},disableSelection:function(){return this.bind((a.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(a){a.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}}),a("<a>").outerWidth(1).jquery||a.each(["Width","Height"],function(c,d){function h(b,c,d,f){return a.each(e,function(){c-=parseFloat(a.curCSS(b,"padding"+this,!0))||0,d&&(c-=parseFloat(a.curCSS(b,"border"+this+"Width",!0))||0),f&&(c-=parseFloat(a.curCSS(b,"margin"+this,!0))||0)}),c}var e=d==="Width"?["Left","Right"]:["Top","Bottom"],f=d.toLowerCase(),g={innerWidth:a.fn.innerWidth,innerHeight:a.fn.innerHeight,outerWidth:a.fn.outerWidth,outerHeight:a.fn.outerHeight};a.fn["inner"+d]=function(c){return c===b?g["inner"+d].call(this):this.each(function(){a(this).css(f,h(this,c)+"px")})},a.fn["outer"+d]=function(b,c){return typeof b!="number"?g["outer"+d].call(this,b):this.each(function(){a(this).css(f,h(this,b,!0,c)+"px")})}}),a.extend(a.expr[":"],{data:a.expr.createPseudo?a.expr.createPseudo(function(b){return function(c){return!!a.data(c,b)}}):function(b,c,d){return!!a.data(b,d[3])},focusable:function(b){return c(b,!isNaN(a.attr(b,"tabindex")))},tabbable:function(b){var d=a.attr(b,"tabindex"),e=isNaN(d);return(e||d>=0)&&c(b,!e)}}),a(function(){var b=document.body,c=b.appendChild(c=document.createElement("div"));c.offsetHeight,a.extend(c.style,{minHeight:"100px",height:"auto",padding:0,borderWidth:0}),a.support.minHeight=c.offsetHeight===100,a.support.selectstart="onselectstart"in c,b.removeChild(c).style.display="none"}),a.curCSS||(a.curCSS=a.css),a.extend(a.ui,{plugin:{add:function(b,c,d){var e=a.ui[b].prototype;for(var f in d)e.plugins[f]=e.plugins[f]||[],e.plugins[f].push([c,d[f]])},call:function(a,b,c){var d=a.plugins[b];if(!d||!a.element[0].parentNode)return;for(var e=0;e<d.length;e++)a.options[d[e][0]]&&d[e][1].apply(a.element,c)}},contains:function(a,b){return document.compareDocumentPosition?a.compareDocumentPosition(b)&16:a!==b&&a.contains(b)},hasScroll:function(b,c){if(a(b).css("overflow")==="hidden")return!1;var d=c&&c==="left"?"scrollLeft":"scrollTop",e=!1;return b[d]>0?!0:(b[d]=1,e=b[d]>0,b[d]=0,e)},isOverAxis:function(a,b,c){return a>b&&a<b+c},isOver:function(b,c,d,e,f,g){return a.ui.isOverAxis(b,d,f)&&a.ui.isOverAxis(c,e,g)}})})(jQuery);;/*! jQuery UI - v1.8.23 - 2012-08-15
* https://github.com/jquery/jquery-ui
* Includes: jquery.ui.datepicker.js
* Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function($,undefined){function Datepicker(){this.debug=!1,this._curInst=null,this._keyEvent=!1,this._disabledInputs=[],this._datepickerShowing=!1,this._inDialog=!1,this._mainDivId="ui-datepicker-div",this._inlineClass="ui-datepicker-inline",this._appendClass="ui-datepicker-append",this._triggerClass="ui-datepicker-trigger",this._dialogClass="ui-datepicker-dialog",this._disableClass="ui-datepicker-disabled",this._unselectableClass="ui-datepicker-unselectable",this._currentClass="ui-datepicker-current-day",this._dayOverClass="ui-datepicker-days-cell-over",this.regional=[],this.regional[""]={closeText:"Done",prevText:"Prev",nextText:"Next",currentText:"Today",monthNames:["January","February","March","April","May","June","July","August","September","October","November","December"],monthNamesShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],dayNames:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],dayNamesShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],dayNamesMin:["Su","Mo","Tu","We","Th","Fr","Sa"],weekHeader:"Wk",dateFormat:"mm/dd/yy",firstDay:0,isRTL:!1,showMonthAfterYear:!1,yearSuffix:""},this._defaults={showOn:"focus",showAnim:"fadeIn",showOptions:{},defaultDate:null,appendText:"",buttonText:"...",buttonImage:"",buttonImageOnly:!1,hideIfNoPrevNext:!1,navigationAsDateFormat:!1,gotoCurrent:!1,changeMonth:!1,changeYear:!1,yearRange:"c-10:c+10",showOtherMonths:!1,selectOtherMonths:!1,showWeek:!1,calculateWeek:this.iso8601Week,shortYearCutoff:"+10",minDate:null,maxDate:null,duration:"fast",beforeShowDay:null,beforeShow:null,onSelect:null,onChangeMonthYear:null,onClose:null,numberOfMonths:1,showCurrentAtPos:0,stepMonths:1,stepBigMonths:12,altField:"",altFormat:"",constrainInput:!0,showButtonPanel:!1,autoSize:!1,disabled:!1},$.extend(this._defaults,this.regional[""]),this.dpDiv=bindHover($('<div id="'+this._mainDivId+'" class="ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"></div>'))}function bindHover(a){var b="button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a";return a.bind("mouseout",function(a){var c=$(a.target).closest(b);if(!c.length)return;c.removeClass("ui-state-hover ui-datepicker-prev-hover ui-datepicker-next-hover")}).bind("mouseover",function(c){var d=$(c.target).closest(b);if($.datepicker._isDisabledDatepicker(instActive.inline?a.parent()[0]:instActive.input[0])||!d.length)return;d.parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover"),d.addClass("ui-state-hover"),d.hasClass("ui-datepicker-prev")&&d.addClass("ui-datepicker-prev-hover"),d.hasClass("ui-datepicker-next")&&d.addClass("ui-datepicker-next-hover")})}function extendRemove(a,b){$.extend(a,b);for(var c in b)if(b[c]==null||b[c]==undefined)a[c]=b[c];return a}function isArray(a){return a&&($.browser.safari&&typeof a=="object"&&a.length||a.constructor&&a.constructor.toString().match(/\Array\(\)/))}$.extend($.ui,{datepicker:{version:"1.8.23"}});var PROP_NAME="datepicker",dpuuid=(new Date).getTime(),instActive;$.extend(Datepicker.prototype,{markerClassName:"hasDatepicker",maxRows:4,log:function(){this.debug&&console.log.apply("",arguments)},_widgetDatepicker:function(){return this.dpDiv},setDefaults:function(a){return extendRemove(this._defaults,a||{}),this},_attachDatepicker:function(target,settings){var inlineSettings=null;for(var attrName in this._defaults){var attrValue=target.getAttribute("date:"+attrName);if(attrValue){inlineSettings=inlineSettings||{};try{inlineSettings[attrName]=eval(attrValue)}catch(err){inlineSettings[attrName]=attrValue}}}var nodeName=target.nodeName.toLowerCase(),inline=nodeName=="div"||nodeName=="span";target.id||(this.uuid+=1,target.id="dp"+this.uuid);var inst=this._newInst($(target),inline);inst.settings=$.extend({},settings||{},inlineSettings||{}),nodeName=="input"?this._connectDatepicker(target,inst):inline&&this._inlineDatepicker(target,inst)},_newInst:function(a,b){var c=a[0].id.replace(/([^A-Za-z0-9_-])/g,"\\\\$1");return{id:c,input:a,selectedDay:0,selectedMonth:0,selectedYear:0,drawMonth:0,drawYear:0,inline:b,dpDiv:b?bindHover($('<div class="'+this._inlineClass+' ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"></div>')):this.dpDiv}},_connectDatepicker:function(a,b){var c=$(a);b.append=$([]),b.trigger=$([]);if(c.hasClass(this.markerClassName))return;this._attachments(c,b),c.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp).bind("setData.datepicker",function(a,c,d){b.settings[c]=d}).bind("getData.datepicker",function(a,c){return this._get(b,c)}),this._autoSize(b),$.data(a,PROP_NAME,b),b.settings.disabled&&this._disableDatepicker(a)},_attachments:function(a,b){var c=this._get(b,"appendText"),d=this._get(b,"isRTL");b.append&&b.append.remove(),c&&(b.append=$('<span class="'+this._appendClass+'">'+c+"</span>"),a[d?"before":"after"](b.append)),a.unbind("focus",this._showDatepicker),b.trigger&&b.trigger.remove();var e=this._get(b,"showOn");(e=="focus"||e=="both")&&a.focus(this._showDatepicker);if(e=="button"||e=="both"){var f=this._get(b,"buttonText"),g=this._get(b,"buttonImage");b.trigger=$(this._get(b,"buttonImageOnly")?$("<img/>").addClass(this._triggerClass).attr({src:g,alt:f,title:f}):$('<button type="button"></button>').addClass(this._triggerClass).html(g==""?f:$("<img/>").attr({src:g,alt:f,title:f}))),a[d?"before":"after"](b.trigger),b.trigger.click(function(){return $.datepicker._datepickerShowing&&$.datepicker._lastInput==a[0]?$.datepicker._hideDatepicker():$.datepicker._datepickerShowing&&$.datepicker._lastInput!=a[0]?($.datepicker._hideDatepicker(),$.datepicker._showDatepicker(a[0])):$.datepicker._showDatepicker(a[0]),!1})}},_autoSize:function(a){if(this._get(a,"autoSize")&&!a.inline){var b=new Date(2009,11,20),c=this._get(a,"dateFormat");if(c.match(/[DM]/)){var d=function(a){var b=0,c=0;for(var d=0;d<a.length;d++)a[d].length>b&&(b=a[d].length,c=d);return c};b.setMonth(d(this._get(a,c.match(/MM/)?"monthNames":"monthNamesShort"))),b.setDate(d(this._get(a,c.match(/DD/)?"dayNames":"dayNamesShort"))+20-b.getDay())}a.input.attr("size",this._formatDate(a,b).length)}},_inlineDatepicker:function(a,b){var c=$(a);if(c.hasClass(this.markerClassName))return;c.addClass(this.markerClassName).append(b.dpDiv).bind("setData.datepicker",function(a,c,d){b.settings[c]=d}).bind("getData.datepicker",function(a,c){return this._get(b,c)}),$.data(a,PROP_NAME,b),this._setDate(b,this._getDefaultDate(b),!0),this._updateDatepicker(b),this._updateAlternate(b),b.settings.disabled&&this._disableDatepicker(a),b.dpDiv.css("display","block")},_dialogDatepicker:function(a,b,c,d,e){var f=this._dialogInst;if(!f){this.uuid+=1;var g="dp"+this.uuid;this._dialogInput=$('<input type="text" id="'+g+'" style="position: absolute; top: -100px; width: 0px;"/>'),this._dialogInput.keydown(this._doKeyDown),$("body").append(this._dialogInput),f=this._dialogInst=this._newInst(this._dialogInput,!1),f.settings={},$.data(this._dialogInput[0],PROP_NAME,f)}extendRemove(f.settings,d||{}),b=b&&b.constructor==Date?this._formatDate(f,b):b,this._dialogInput.val(b),this._pos=e?e.length?e:[e.pageX,e.pageY]:null;if(!this._pos){var h=document.documentElement.clientWidth,i=document.documentElement.clientHeight,j=document.documentElement.scrollLeft||document.body.scrollLeft,k=document.documentElement.scrollTop||document.body.scrollTop;this._pos=[h/2-100+j,i/2-150+k]}return this._dialogInput.css("left",this._pos[0]+20+"px").css("top",this._pos[1]+"px"),f.settings.onSelect=c,this._inDialog=!0,this.dpDiv.addClass(this._dialogClass),this._showDatepicker(this._dialogInput[0]),$.blockUI&&$.blockUI(this.dpDiv),$.data(this._dialogInput[0],PROP_NAME,f),this},_destroyDatepicker:function(a){var b=$(a),c=$.data(a,PROP_NAME);if(!b.hasClass(this.markerClassName))return;var d=a.nodeName.toLowerCase();$.removeData(a,PROP_NAME),d=="input"?(c.append.remove(),c.trigger.remove(),b.removeClass(this.markerClassName).unbind("focus",this._showDatepicker).unbind("keydown",this._doKeyDown).unbind("keypress",this._doKeyPress).unbind("keyup",this._doKeyUp)):(d=="div"||d=="span")&&b.removeClass(this.markerClassName).empty()},_enableDatepicker:function(a){var b=$(a),c=$.data(a,PROP_NAME);if(!b.hasClass(this.markerClassName))return;var d=a.nodeName.toLowerCase();if(d=="input")a.disabled=!1,c.trigger.filter("button").each(function(){this.disabled=!1}).end().filter("img").css({opacity:"1.0",cursor:""});else if(d=="div"||d=="span"){var e=b.children("."+this._inlineClass);e.children().removeClass("ui-state-disabled"),e.find("select.ui-datepicker-month, select.ui-datepicker-year").removeAttr("disabled")}this._disabledInputs=$.map(this._disabledInputs,function(b){return b==a?null:b})},_disableDatepicker:function(a){var b=$(a),c=$.data(a,PROP_NAME);if(!b.hasClass(this.markerClassName))return;var d=a.nodeName.toLowerCase();if(d=="input")a.disabled=!0,c.trigger.filter("button").each(function(){this.disabled=!0}).end().filter("img").css({opacity:"0.5",cursor:"default"});else if(d=="div"||d=="span"){var e=b.children("."+this._inlineClass);e.children().addClass("ui-state-disabled"),e.find("select.ui-datepicker-month, select.ui-datepicker-year").attr("disabled","disabled")}this._disabledInputs=$.map(this._disabledInputs,function(b){return b==a?null:b}),this._disabledInputs[this._disabledInputs.length]=a},_isDisabledDatepicker:function(a){if(!a)return!1;for(var b=0;b<this._disabledInputs.length;b++)if(this._disabledInputs[b]==a)return!0;return!1},_getInst:function(a){try{return $.data(a,PROP_NAME)}catch(b){throw"Missing instance data for this datepicker"}},_optionDatepicker:function(a,b,c){var d=this._getInst(a);if(arguments.length==2&&typeof b=="string")return b=="defaults"?$.extend({},$.datepicker._defaults):d?b=="all"?$.extend({},d.settings):this._get(d,b):null;var e=b||{};typeof b=="string"&&(e={},e[b]=c);if(d){this._curInst==d&&this._hideDatepicker();var f=this._getDateDatepicker(a,!0),g=this._getMinMaxDate(d,"min"),h=this._getMinMaxDate(d,"max");extendRemove(d.settings,e),g!==null&&e.dateFormat!==undefined&&e.minDate===undefined&&(d.settings.minDate=this._formatDate(d,g)),h!==null&&e.dateFormat!==undefined&&e.maxDate===undefined&&(d.settings.maxDate=this._formatDate(d,h)),this._attachments($(a),d),this._autoSize(d),this._setDate(d,f),this._updateAlternate(d),this._updateDatepicker(d)}},_changeDatepicker:function(a,b,c){this._optionDatepicker(a,b,c)},_refreshDatepicker:function(a){var b=this._getInst(a);b&&this._updateDatepicker(b)},_setDateDatepicker:function(a,b){var c=this._getInst(a);c&&(this._setDate(c,b),this._updateDatepicker(c),this._updateAlternate(c))},_getDateDatepicker:function(a,b){var c=this._getInst(a);return c&&!c.inline&&this._setDateFromField(c,b),c?this._getDate(c):null},_doKeyDown:function(a){var b=$.datepicker._getInst(a.target),c=!0,d=b.dpDiv.is(".ui-datepicker-rtl");b._keyEvent=!0;if($.datepicker._datepickerShowing)switch(a.keyCode){case 9:$.datepicker._hideDatepicker(),c=!1;break;case 13:var e=$("td."+$.datepicker._dayOverClass+":not(."+$.datepicker._currentClass+")",b.dpDiv);e[0]&&$.datepicker._selectDay(a.target,b.selectedMonth,b.selectedYear,e[0]);var f=$.datepicker._get(b,"onSelect");if(f){var g=$.datepicker._formatDate(b);f.apply(b.input?b.input[0]:null,[g,b])}else $.datepicker._hideDatepicker();return!1;case 27:$.datepicker._hideDatepicker();break;case 33:$.datepicker._adjustDate(a.target,a.ctrlKey?-$.datepicker._get(b,"stepBigMonths"):-$.datepicker._get(b,"stepMonths"),"M");break;case 34:$.datepicker._adjustDate(a.target,a.ctrlKey?+$.datepicker._get(b,"stepBigMonths"):+$.datepicker._get(b,"stepMonths"),"M");break;case 35:(a.ctrlKey||a.metaKey)&&$.datepicker._clearDate(a.target),c=a.ctrlKey||a.metaKey;break;case 36:(a.ctrlKey||a.metaKey)&&$.datepicker._gotoToday(a.target),c=a.ctrlKey||a.metaKey;break;case 37:(a.ctrlKey||a.metaKey)&&$.datepicker._adjustDate(a.target,d?1:-1,"D"),c=a.ctrlKey||a.metaKey,a.originalEvent.altKey&&$.datepicker._adjustDate(a.target,a.ctrlKey?-$.datepicker._get(b,"stepBigMonths"):-$.datepicker._get(b,"stepMonths"),"M");break;case 38:(a.ctrlKey||a.metaKey)&&$.datepicker._adjustDate(a.target,-7,"D"),c=a.ctrlKey||a.metaKey;break;case 39:(a.ctrlKey||a.metaKey)&&$.datepicker._adjustDate(a.target,d?-1:1,"D"),c=a.ctrlKey||a.metaKey,a.originalEvent.altKey&&$.datepicker._adjustDate(a.target,a.ctrlKey?+$.datepicker._get(b,"stepBigMonths"):+$.datepicker._get(b,"stepMonths"),"M");break;case 40:(a.ctrlKey||a.metaKey)&&$.datepicker._adjustDate(a.target,7,"D"),c=a.ctrlKey||a.metaKey;break;default:c=!1}else a.keyCode==36&&a.ctrlKey?$.datepicker._showDatepicker(this):c=!1;c&&(a.preventDefault(),a.stopPropagation())},_doKeyPress:function(a){var b=$.datepicker._getInst(a.target);if($.datepicker._get(b,"constrainInput")){var c=$.datepicker._possibleChars($.datepicker._get(b,"dateFormat")),d=String.fromCharCode(a.charCode==undefined?a.keyCode:a.charCode);return a.ctrlKey||a.metaKey||d<" "||!c||c.indexOf(d)>-1}},_doKeyUp:function(a){var b=$.datepicker._getInst(a.target);if(b.input.val()!=b.lastVal)try{var c=$.datepicker.parseDate($.datepicker._get(b,"dateFormat"),b.input?b.input.val():null,$.datepicker._getFormatConfig(b));c&&($.datepicker._setDateFromField(b),$.datepicker._updateAlternate(b),$.datepicker._updateDatepicker(b))}catch(d){$.datepicker.log(d)}return!0},_showDatepicker:function(a){a=a.target||a,a.nodeName.toLowerCase()!="input"&&(a=$("input",a.parentNode)[0]);if($.datepicker._isDisabledDatepicker(a)||$.datepicker._lastInput==a)return;var b=$.datepicker._getInst(a);$.datepicker._curInst&&$.datepicker._curInst!=b&&($.datepicker._curInst.dpDiv.stop(!0,!0),b&&$.datepicker._datepickerShowing&&$.datepicker._hideDatepicker($.datepicker._curInst.input[0]));var c=$.datepicker._get(b,"beforeShow"),d=c?c.apply(a,[a,b]):{};if(d===!1)return;extendRemove(b.settings,d),b.lastVal=null,$.datepicker._lastInput=a,$.datepicker._setDateFromField(b),$.datepicker._inDialog&&(a.value=""),$.datepicker._pos||($.datepicker._pos=$.datepicker._findPos(a),$.datepicker._pos[1]+=a.offsetHeight);var e=!1;$(a).parents().each(function(){return e|=$(this).css("position")=="fixed",!e}),e&&$.browser.opera&&($.datepicker._pos[0]-=document.documentElement.scrollLeft,$.datepicker._pos[1]-=document.documentElement.scrollTop);var f={left:$.datepicker._pos[0],top:$.datepicker._pos[1]};$.datepicker._pos=null,b.dpDiv.empty(),b.dpDiv.css({position:"absolute",display:"block",top:"-1000px"}),$.datepicker._updateDatepicker(b),f=$.datepicker._checkOffset(b,f,e),b.dpDiv.css({position:$.datepicker._inDialog&&$.blockUI?"static":e?"fixed":"absolute",display:"none",left:f.left+"px",top:f.top+"px"});if(!b.inline){var g=$.datepicker._get(b,"showAnim"),h=$.datepicker._get(b,"duration"),i=function(){var a=b.dpDiv.find("iframe.ui-datepicker-cover");if(!!a.length){var c=$.datepicker._getBorders(b.dpDiv);a.css({left:-c[0],top:-c[1],width:b.dpDiv.outerWidth(),height:b.dpDiv.outerHeight()})}};b.dpDiv.zIndex($(a).zIndex()+1),$.datepicker._datepickerShowing=!0,$.effects&&$.effects[g]?b.dpDiv.show(g,$.datepicker._get(b,"showOptions"),h,i):b.dpDiv[g||"show"](g?h:null,i),(!g||!h)&&i(),b.input.is(":visible")&&!b.input.is(":disabled")&&b.input.focus(),$.datepicker._curInst=b}},_updateDatepicker:function(a){var b=this;b.maxRows=4;var c=$.datepicker._getBorders(a.dpDiv);instActive=a,a.dpDiv.empty().append(this._generateHTML(a)),this._attachHandlers(a);var d=a.dpDiv.find("iframe.ui-datepicker-cover");!d.length||d.css({left:-c[0],top:-c[1],width:a.dpDiv.outerWidth(),height:a.dpDiv.outerHeight()}),a.dpDiv.find("."+this._dayOverClass+" a").mouseover();var e=this._getNumberOfMonths(a),f=e[1],g=17;a.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width(""),f>1&&a.dpDiv.addClass("ui-datepicker-multi-"+f).css("width",g*f+"em"),a.dpDiv[(e[0]!=1||e[1]!=1?"add":"remove")+"Class"]("ui-datepicker-multi"),a.dpDiv[(this._get(a,"isRTL")?"add":"remove")+"Class"]("ui-datepicker-rtl"),a==$.datepicker._curInst&&$.datepicker._datepickerShowing&&a.input&&a.input.is(":visible")&&!a.input.is(":disabled")&&a.input[0]!=document.activeElement&&a.input.focus();if(a.yearshtml){var h=a.yearshtml;setTimeout(function(){h===a.yearshtml&&a.yearshtml&&a.dpDiv.find("select.ui-datepicker-year:first").replaceWith(a.yearshtml),h=a.yearshtml=null},0)}},_getBorders:function(a){var b=function(a){return{thin:1,medium:2,thick:3}[a]||a};return[parseFloat(b(a.css("border-left-width"))),parseFloat(b(a.css("border-top-width")))]},_checkOffset:function(a,b,c){var d=a.dpDiv.outerWidth(),e=a.dpDiv.outerHeight(),f=a.input?a.input.outerWidth():0,g=a.input?a.input.outerHeight():0,h=document.documentElement.clientWidth+(c?0:$(document).scrollLeft()),i=document.documentElement.clientHeight+(c?0:$(document).scrollTop());return b.left-=this._get(a,"isRTL")?d-f:0,b.left-=c&&b.left==a.input.offset().left?$(document).scrollLeft():0,b.top-=c&&b.top==a.input.offset().top+g?$(document).scrollTop():0,b.left-=Math.min(b.left,b.left+d>h&&h>d?Math.abs(b.left+d-h):0),b.top-=Math.min(b.top,b.top+e>i&&i>e?Math.abs(e+g):0),b},_findPos:function(a){var b=this._getInst(a),c=this._get(b,"isRTL");while(a&&(a.type=="hidden"||a.nodeType!=1||$.expr.filters.hidden(a)))a=a[c?"previousSibling":"nextSibling"];var d=$(a).offset();return[d.left,d.top]},_hideDatepicker:function(a){var b=this._curInst;if(!b||a&&b!=$.data(a,PROP_NAME))return;if(this._datepickerShowing){var c=this._get(b,"showAnim"),d=this._get(b,"duration"),e=function(){$.datepicker._tidyDialog(b)};$.effects&&$.effects[c]?b.dpDiv.hide(c,$.datepicker._get(b,"showOptions"),d,e):b.dpDiv[c=="slideDown"?"slideUp":c=="fadeIn"?"fadeOut":"hide"](c?d:null,e),c||e(),this._datepickerShowing=!1;var f=this._get(b,"onClose");f&&f.apply(b.input?b.input[0]:null,[b.input?b.input.val():"",b]),this._lastInput=null,this._inDialog&&(this._dialogInput.css({position:"absolute",left:"0",top:"-100px"}),$.blockUI&&($.unblockUI(),$("body").append(this.dpDiv))),this._inDialog=!1}},_tidyDialog:function(a){a.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar")},_checkExternalClick:function(a){if(!$.datepicker._curInst)return;var b=$(a.target),c=$.datepicker._getInst(b[0]);(b[0].id!=$.datepicker._mainDivId&&b.parents("#"+$.datepicker._mainDivId).length==0&&!b.hasClass($.datepicker.markerClassName)&&!b.closest("."+$.datepicker._triggerClass).length&&$.datepicker._datepickerShowing&&(!$.datepicker._inDialog||!$.blockUI)||b.hasClass($.datepicker.markerClassName)&&$.datepicker._curInst!=c)&&$.datepicker._hideDatepicker()},_adjustDate:function(a,b,c){var d=$(a),e=this._getInst(d[0]);if(this._isDisabledDatepicker(d[0]))return;this._adjustInstDate(e,b+(c=="M"?this._get(e,"showCurrentAtPos"):0),c),this._updateDatepicker(e)},_gotoToday:function(a){var b=$(a),c=this._getInst(b[0]);if(this._get(c,"gotoCurrent")&&c.currentDay)c.selectedDay=c.currentDay,c.drawMonth=c.selectedMonth=c.currentMonth,c.drawYear=c.selectedYear=c.currentYear;else{var d=new Date;c.selectedDay=d.getDate(),c.drawMonth=c.selectedMonth=d.getMonth(),c.drawYear=c.selectedYear=d.getFullYear()}this._notifyChange(c),this._adjustDate(b)},_selectMonthYear:function(a,b,c){var d=$(a),e=this._getInst(d[0]);e["selected"+(c=="M"?"Month":"Year")]=e["draw"+(c=="M"?"Month":"Year")]=parseInt(b.options[b.selectedIndex].value,10),this._notifyChange(e),this._adjustDate(d)},_selectDay:function(a,b,c,d){var e=$(a);if($(d).hasClass(this._unselectableClass)||this._isDisabledDatepicker(e[0]))return;var f=this._getInst(e[0]);f.selectedDay=f.currentDay=$("a",d).html(),f.selectedMonth=f.currentMonth=b,f.selectedYear=f.currentYear=c,this._selectDate(a,this._formatDate(f,f.currentDay,f.currentMonth,f.currentYear))},_clearDate:function(a){var b=$(a),c=this._getInst(b[0]);this._selectDate(b,"")},_selectDate:function(a,b){var c=$(a),d=this._getInst(c[0]);b=b!=null?b:this._formatDate(d),d.input&&d.input.val(b),this._updateAlternate(d);var e=this._get(d,"onSelect");e?e.apply(d.input?d.input[0]:null,[b,d]):d.input&&d.input.trigger("change"),d.inline?this._updateDatepicker(d):(this._hideDatepicker(),this._lastInput=d.input[0],typeof d.input[0]!="object"&&d.input.focus(),this._lastInput=null)},_updateAlternate:function(a){var b=this._get(a,"altField");if(b){var c=this._get(a,"altFormat")||this._get(a,"dateFormat"),d=this._getDate(a),e=this.formatDate(c,d,this._getFormatConfig(a));$(b).each(function(){$(this).val(e)})}},noWeekends:function(a){var b=a.getDay();return[b>0&&b<6,""]},iso8601Week:function(a){var b=new Date(a.getTime());b.setDate(b.getDate()+4-(b.getDay()||7));var c=b.getTime();return b.setMonth(0),b.setDate(1),Math.floor(Math.round((c-b)/864e5)/7)+1},parseDate:function(a,b,c){if(a==null||b==null)throw"Invalid arguments";b=typeof b=="object"?b.toString():b+"";if(b=="")return null;var d=(c?c.shortYearCutoff:null)||this._defaults.shortYearCutoff;d=typeof d!="string"?d:(new Date).getFullYear()%100+parseInt(d,10);var e=(c?c.dayNamesShort:null)||this._defaults.dayNamesShort,f=(c?c.dayNames:null)||this._defaults.dayNames,g=(c?c.monthNamesShort:null)||this._defaults.monthNamesShort,h=(c?c.monthNames:null)||this._defaults.monthNames,i=-1,j=-1,k=-1,l=-1,m=!1,n=function(b){var c=s+1<a.length&&a.charAt(s+1)==b;return c&&s++,c},o=function(a){var c=n(a),d=a=="@"?14:a=="!"?20:a=="y"&&c?4:a=="o"?3:2,e=new RegExp("^\\d{1,"+d+"}"),f=b.substring(r).match(e);if(!f)throw"Missing number at position "+r;return r+=f[0].length,parseInt(f[0],10)},p=function(a,c,d){var e=$.map(n(a)?d:c,function(a,b){return[[b,a]]}).sort(function(a,b){return-(a[1].length-b[1].length)}),f=-1;$.each(e,function(a,c){var d=c[1];if(b.substr(r,d.length).toLowerCase()==d.toLowerCase())return f=c[0],r+=d.length,!1});if(f!=-1)return f+1;throw"Unknown name at position "+r},q=function(){if(b.charAt(r)!=a.charAt(s))throw"Unexpected literal at position "+r;r++},r=0;for(var s=0;s<a.length;s++)if(m)a.charAt(s)=="'"&&!n("'")?m=!1:q();else switch(a.charAt(s)){case"d":k=o("d");break;case"D":p("D",e,f);break;case"o":l=o("o");break;case"m":j=o("m");break;case"M":j=p("M",g,h);break;case"y":i=o("y");break;case"@":var t=new Date(o("@"));i=t.getFullYear(),j=t.getMonth()+1,k=t.getDate();break;case"!":var t=new Date((o("!")-this._ticksTo1970)/1e4);i=t.getFullYear(),j=t.getMonth()+1,k=t.getDate();break;case"'":n("'")?q():m=!0;break;default:q()}if(r<b.length)throw"Extra/unparsed characters found in date: "+b.substring(r);i==-1?i=(new Date).getFullYear():i<100&&(i+=(new Date).getFullYear()-(new Date).getFullYear()%100+(i<=d?0:-100));if(l>-1){j=1,k=l;do{var u=this._getDaysInMonth(i,j-1);if(k<=u)break;j++,k-=u}while(!0)}var t=this._daylightSavingAdjust(new Date(i,j-1,k));if(t.getFullYear()!=i||t.getMonth()+1!=j||t.getDate()!=k)throw"Invalid date";return t},ATOM:"yy-mm-dd",COOKIE:"D, dd M yy",ISO_8601:"yy-mm-dd",RFC_822:"D, d M y",RFC_850:"DD, dd-M-y",RFC_1036:"D, d M y",RFC_1123:"D, d M yy",RFC_2822:"D, d M yy",RSS:"D, d M y",TICKS:"!",TIMESTAMP:"@",W3C:"yy-mm-dd",_ticksTo1970:(718685+Math.floor(492.5)-Math.floor(19.7)+Math.floor(4.925))*24*60*60*1e7,formatDate:function(a,b,c){if(!b)return"";var d=(c?c.dayNamesShort:null)||this._defaults.dayNamesShort,e=(c?c.dayNames:null)||this._defaults.dayNames,f=(c?c.monthNamesShort:null)||this._defaults.monthNamesShort,g=(c?c.monthNames:null)||this._defaults.monthNames,h=function(b){var c=m+1<a.length&&a.charAt(m+1)==b;return c&&m++,c},i=function(a,b,c){var d=""+b;if(h(a))while(d.length<c)d="0"+d;return d},j=function(a,b,c,d){return h(a)?d[b]:c[b]},k="",l=!1;if(b)for(var m=0;m<a.length;m++)if(l)a.charAt(m)=="'"&&!h("'")?l=!1:k+=a.charAt(m);else switch(a.charAt(m)){case"d":k+=i("d",b.getDate(),2);break;case"D":k+=j("D",b.getDay(),d,e);break;case"o":k+=i("o",Math.round(((new Date(b.getFullYear(),b.getMonth(),b.getDate())).getTime()-(new Date(b.getFullYear(),0,0)).getTime())/864e5),3);break;case"m":k+=i("m",b.getMonth()+1,2);break;case"M":k+=j("M",b.getMonth(),f,g);break;case"y":k+=h("y")?b.getFullYear():(b.getYear()%100<10?"0":"")+b.getYear()%100;break;case"@":k+=b.getTime();break;case"!":k+=b.getTime()*1e4+this._ticksTo1970;break;case"'":h("'")?k+="'":l=!0;break;default:k+=a.charAt(m)}return k},_possibleChars:function(a){var b="",c=!1,d=function(b){var c=e+1<a.length&&a.charAt(e+1)==b;return c&&e++,c};for(var e=0;e<a.length;e++)if(c)a.charAt(e)=="'"&&!d("'")?c=!1:b+=a.charAt(e);else switch(a.charAt(e)){case"d":case"m":case"y":case"@":b+="0123456789";break;case"D":case"M":return null;case"'":d("'")?b+="'":c=!0;break;default:b+=a.charAt(e)}return b},_get:function(a,b){return a.settings[b]!==undefined?a.settings[b]:this._defaults[b]},_setDateFromField:function(a,b){if(a.input.val()==a.lastVal)return;var c=this._get(a,"dateFormat"),d=a.lastVal=a.input?a.input.val():null,e,f;e=f=this._getDefaultDate(a);var g=this._getFormatConfig(a);try{e=this.parseDate(c,d,g)||f}catch(h){this.log(h),d=b?"":d}a.selectedDay=e.getDate(),a.drawMonth=a.selectedMonth=e.getMonth(),a.drawYear=a.selectedYear=e.getFullYear(),a.currentDay=d?e.getDate():0,a.currentMonth=d?e.getMonth():0,a.currentYear=d?e.getFullYear():0,this._adjustInstDate(a)},_getDefaultDate:function(a){return this._restrictMinMax(a,this._determineDate(a,this._get(a,"defaultDate"),new Date))},_determineDate:function(a,b,c){var d=function(a){var b=new Date;return b.setDate(b.getDate()+a),b},e=function(b){try{return $.datepicker.parseDate($.datepicker._get(a,"dateFormat"),b,$.datepicker._getFormatConfig(a))}catch(c){}var d=(b.toLowerCase().match(/^c/)?$.datepicker._getDate(a):null)||new Date,e=d.getFullYear(),f=d.getMonth(),g=d.getDate(),h=/([+-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g,i=h.exec(b);while(i){switch(i[2]||"d"){case"d":case"D":g+=parseInt(i[1],10);break;case"w":case"W":g+=parseInt(i[1],10)*7;break;case"m":case"M":f+=parseInt(i[1],10),g=Math.min(g,$.datepicker._getDaysInMonth(e,f));break;case"y":case"Y":e+=parseInt(i[1],10),g=Math.min(g,$.datepicker._getDaysInMonth(e,f))}i=h.exec(b)}return new Date(e,f,g)},f=b==null||b===""?c:typeof b=="string"?e(b):typeof b=="number"?isNaN(b)?c:d(b):new Date(b.getTime());return f=f&&f.toString()=="Invalid Date"?c:f,f&&(f.setHours(0),f.setMinutes(0),f.setSeconds(0),f.setMilliseconds(0)),this._daylightSavingAdjust(f)},_daylightSavingAdjust:function(a){return a?(a.setHours(a.getHours()>12?a.getHours()+2:0),a):null},_setDate:function(a,b,c){var d=!b,e=a.selectedMonth,f=a.selectedYear,g=this._restrictMinMax(a,this._determineDate(a,b,new Date));a.selectedDay=a.currentDay=g.getDate(),a.drawMonth=a.selectedMonth=a.currentMonth=g.getMonth(),a.drawYear=a.selectedYear=a.currentYear=g.getFullYear(),(e!=a.selectedMonth||f!=a.selectedYear)&&!c&&this._notifyChange(a),this._adjustInstDate(a),a.input&&a.input.val(d?"":this._formatDate(a))},_getDate:function(a){var b=!a.currentYear||a.input&&a.input.val()==""?null:this._daylightSavingAdjust(new Date(a.currentYear,a.currentMonth,a.currentDay));return b},_attachHandlers:function(a){var b=this._get(a,"stepMonths"),c="#"+a.id.replace(/\\\\/g,"\\");a.dpDiv.find("[data-handler]").map(function(){var a={prev:function(){window["DP_jQuery_"+dpuuid].datepicker._adjustDate(c,-b,"M")},next:function(){window["DP_jQuery_"+dpuuid].datepicker._adjustDate(c,+b,"M")},hide:function(){window["DP_jQuery_"+dpuuid].datepicker._hideDatepicker()},today:function(){window["DP_jQuery_"+dpuuid].datepicker._gotoToday(c)},selectDay:function(){return window["DP_jQuery_"+dpuuid].datepicker._selectDay(c,+this.getAttribute("data-month"),+this.getAttribute("data-year"),this),!1},selectMonth:function(){return window["DP_jQuery_"+dpuuid].datepicker._selectMonthYear(c,this,"M"),!1},selectYear:function(){return window["DP_jQuery_"+dpuuid].datepicker._selectMonthYear(c,this,"Y"),!1}};$(this).bind(this.getAttribute("data-event"),a[this.getAttribute("data-handler")])})},_generateHTML:function(a){var b=new Date;b=this._daylightSavingAdjust(new Date(b.getFullYear(),b.getMonth(),b.getDate()));var c=this._get(a,"isRTL"),d=this._get(a,"showButtonPanel"),e=this._get(a,"hideIfNoPrevNext"),f=this._get(a,"navigationAsDateFormat"),g=this._getNumberOfMonths(a),h=this._get(a,"showCurrentAtPos"),i=this._get(a,"stepMonths"),j=g[0]!=1||g[1]!=1,k=this._daylightSavingAdjust(a.currentDay?new Date(a.currentYear,a.currentMonth,a.currentDay):new Date(9999,9,9)),l=this._getMinMaxDate(a,"min"),m=this._getMinMaxDate(a,"max"),n=a.drawMonth-h,o=a.drawYear;n<0&&(n+=12,o--);if(m){var p=this._daylightSavingAdjust(new Date(m.getFullYear(),m.getMonth()-g[0]*g[1]+1,m.getDate()));p=l&&p<l?l:p;while(this._daylightSavingAdjust(new Date(o,n,1))>p)n--,n<0&&(n=11,o--)}a.drawMonth=n,a.drawYear=o;var q=this._get(a,"prevText");q=f?this.formatDate(q,this._daylightSavingAdjust(new Date(o,n-i,1)),this._getFormatConfig(a)):q;var r=this._canAdjustMonth(a,-1,o,n)?'<a class="ui-datepicker-prev ui-corner-all" data-handler="prev" data-event="click" title="'+q+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"e":"w")+'">'+q+"</span></a>":e?"":'<a class="ui-datepicker-prev ui-corner-all ui-state-disabled" title="'+q+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"e":"w")+'">'+q+"</span></a>",s=this._get(a,"nextText");s=f?this.formatDate(s,this._daylightSavingAdjust(new Date(o,n+i,1)),this._getFormatConfig(a)):s;var t=this._canAdjustMonth(a,1,o,n)?'<a class="ui-datepicker-next ui-corner-all" data-handler="next" data-event="click" title="'+s+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"w":"e")+'">'+s+"</span></a>":e?"":'<a class="ui-datepicker-next ui-corner-all ui-state-disabled" title="'+s+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"w":"e")+'">'+s+"</span></a>",u=this._get(a,"currentText"),v=this._get(a,"gotoCurrent")&&a.currentDay?k:b;u=f?this.formatDate(u,v,this._getFormatConfig(a)):u;var w=a.inline?"":'<button type="button" class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" data-handler="hide" data-event="click">'+this._get(a,"closeText")+"</button>",x=d?'<div class="ui-datepicker-buttonpane ui-widget-content">'+(c?w:"")+(this._isInRange(a,v)?'<button type="button" class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" data-handler="today" data-event="click">'+u+"</button>":"")+(c?"":w)+"</div>":"",y=parseInt(this._get(a,"firstDay"),10);y=isNaN(y)?0:y;var z=this._get(a,"showWeek"),A=this._get(a,"dayNames"),B=this._get(a,"dayNamesShort"),C=this._get(a,"dayNamesMin"),D=this._get(a,"monthNames"),E=this._get(a,"monthNamesShort"),F=this._get(a,"beforeShowDay"),G=this._get(a,"showOtherMonths"),H=this._get(a,"selectOtherMonths"),I=this._get(a,"calculateWeek")||this.iso8601Week,J=this._getDefaultDate(a),K="";for(var L=0;L<g[0];L++){var M="";this.maxRows=4;for(var N=0;N<g[1];N++){var O=this._daylightSavingAdjust(new Date(o,n,a.selectedDay)),P=" ui-corner-all",Q="";if(j){Q+='<div class="ui-datepicker-group';if(g[1]>1)switch(N){case 0:Q+=" ui-datepicker-group-first",P=" ui-corner-"+(c?"right":"left");break;case g[1]-1:Q+=" ui-datepicker-group-last",P=" ui-corner-"+(c?"left":"right");break;default:Q+=" ui-datepicker-group-middle",P=""}Q+='">'}Q+='<div class="ui-datepicker-header ui-widget-header ui-helper-clearfix'+P+'">'+(/all|left/.test(P)&&L==0?c?t:r:"")+(/all|right/.test(P)&&L==0?c?r:t:"")+this._generateMonthYearHeader(a,n,o,l,m,L>0||N>0,D,E)+'</div><table class="ui-datepicker-calendar"><thead>'+"<tr>";var R=z?'<th class="ui-datepicker-week-col">'+this._get(a,"weekHeader")+"</th>":"";for(var S=0;S<7;S++){var T=(S+y)%7;R+="<th"+((S+y+6)%7>=5?' class="ui-datepicker-week-end"':"")+">"+'<span title="'+A[T]+'">'+C[T]+"</span></th>"}Q+=R+"</tr></thead><tbody>";var U=this._getDaysInMonth(o,n);o==a.selectedYear&&n==a.selectedMonth&&(a.selectedDay=Math.min(a.selectedDay,U));var V=(this._getFirstDayOfMonth(o,n)-y+7)%7,W=Math.ceil((V+U)/7),X=j?this.maxRows>W?this.maxRows:W:W;this.maxRows=X;var Y=this._daylightSavingAdjust(new Date(o,n,1-V));for(var Z=0;Z<X;Z++){Q+="<tr>";var _=z?'<td class="ui-datepicker-week-col">'+this._get(a,"calculateWeek")(Y)+"</td>":"";for(var S=0;S<7;S++){var ba=F?F.apply(a.input?a.input[0]:null,[Y]):[!0,""],bb=Y.getMonth()!=n,bc=bb&&!H||!ba[0]||l&&Y<l||m&&Y>m;_+='<td class="'+((S+y+6)%7>=5?" ui-datepicker-week-end":"")+(bb?" ui-datepicker-other-month":"")+(Y.getTime()==O.getTime()&&n==a.selectedMonth&&a._keyEvent||J.getTime()==Y.getTime()&&J.getTime()==O.getTime()?" "+this._dayOverClass:"")+(bc?" "+this._unselectableClass+" ui-state-disabled":"")+(bb&&!G?"":" "+ba[1]+(Y.getTime()==k.getTime()?" "+this._currentClass:"")+(Y.getTime()==b.getTime()?" ui-datepicker-today":""))+'"'+((!bb||G)&&ba[2]?' title="'+ba[2]+'"':"")+(bc?"":' data-handler="selectDay" data-event="click" data-month="'+Y.getMonth()+'" data-year="'+Y.getFullYear()+'"')+">"+(bb&&!G?"&#xa0;":bc?'<span class="ui-state-default">'+Y.getDate()+"</span>":'<a class="ui-state-default'+(Y.getTime()==b.getTime()?" ui-state-highlight":"")+(Y.getTime()==k.getTime()?" ui-state-active":"")+(bb?" ui-priority-secondary":"")+'" href="#">'+Y.getDate()+"</a>")+"</td>",Y.setDate(Y.getDate()+1),Y=this._daylightSavingAdjust(Y)}Q+=_+"</tr>"}n++,n>11&&(n=0,o++),Q+="</tbody></table>"+(j?"</div>"+(g[0]>0&&N==g[1]-1?'<div class="ui-datepicker-row-break"></div>':""):""),M+=Q}K+=M}return K+=x+($.browser.msie&&parseInt($.browser.version,10)<7&&!a.inline?'<iframe src="javascript:false;" class="ui-datepicker-cover" frameborder="0"></iframe>':""),a._keyEvent=!1,K},_generateMonthYearHeader:function(a,b,c,d,e,f,g,h){var i=this._get(a,"changeMonth"),j=this._get(a,"changeYear"),k=this._get(a,"showMonthAfterYear"),l='<div class="ui-datepicker-title">',m="";if(f||!i)m+='<span class="ui-datepicker-month">'+g[b]+"</span>";else{var n=d&&d.getFullYear()==c,o=e&&e.getFullYear()==c;m+='<select class="ui-datepicker-month" data-handler="selectMonth" data-event="change">';for(var p=0;p<12;p++)(!n||p>=d.getMonth())&&(!o||p<=e.getMonth())&&(m+='<option value="'+p+'"'+(p==b?' selected="selected"':"")+">"+h[p]+"</option>");m+="</select>"}k||(l+=m+(f||!i||!j?"&#xa0;":""));if(!a.yearshtml){a.yearshtml="";if(f||!j)l+='<span class="ui-datepicker-year">'+c+"</span>";else{var q=this._get(a,"yearRange").split(":"),r=(new Date).getFullYear(),s=function(a){var b=a.match(/c[+-].*/)?c+parseInt(a.substring(1),10):a.match(/[+-].*/)?r+parseInt(a,10):parseInt(a,10);return isNaN(b)?r:b},t=s(q[0]),u=Math.max(t,s(q[1]||""));t=d?Math.max(t,d.getFullYear()):t,u=e?Math.min(u,e.getFullYear()):u,a.yearshtml+='<select class="ui-datepicker-year" data-handler="selectYear" data-event="change">';for(;t<=u;t++)a.yearshtml+='<option value="'+t+'"'+(t==c?' selected="selected"':"")+">"+t+"</option>";a.yearshtml+="</select>",l+=a.yearshtml,a.yearshtml=null}}return l+=this._get(a,"yearSuffix"),k&&(l+=(f||!i||!j?"&#xa0;":"")+m),l+="</div>",l},_adjustInstDate:function(a,b,c){var d=a.drawYear+(c=="Y"?b:0),e=a.drawMonth+(c=="M"?b:0),f=Math.min(a.selectedDay,this._getDaysInMonth(d,e))+(c=="D"?b:0),g=this._restrictMinMax(a,this._daylightSavingAdjust(new Date(d,e,f)));a.selectedDay=g.getDate(),a.drawMonth=a.selectedMonth=g.getMonth(),a.drawYear=a.selectedYear=g.getFullYear(),(c=="M"||c=="Y")&&this._notifyChange(a)},_restrictMinMax:function(a,b){var c=this._getMinMaxDate(a,"min"),d=this._getMinMaxDate(a,"max"),e=c&&b<c?c:b;return e=d&&e>d?d:e,e},_notifyChange:function(a){var b=this._get(a,"onChangeMonthYear");b&&b.apply(a.input?a.input[0]:null,[a.selectedYear,a.selectedMonth+1,a])},_getNumberOfMonths:function(a){var b=this._get(a,"numberOfMonths");return b==null?[1,1]:typeof b=="number"?[1,b]:b},_getMinMaxDate:function(a,b){return this._determineDate(a,this._get(a,b+"Date"),null)},_getDaysInMonth:function(a,b){return 32-this._daylightSavingAdjust(new Date(a,b,32)).getDate()},_getFirstDayOfMonth:function(a,b){return(new Date(a,b,1)).getDay()},_canAdjustMonth:function(a,b,c,d){var e=this._getNumberOfMonths(a),f=this._daylightSavingAdjust(new Date(c,d+(b<0?b:e[0]*e[1]),1));return b<0&&f.setDate(this._getDaysInMonth(f.getFullYear(),f.getMonth())),this._isInRange(a,f)},_isInRange:function(a,b){var c=this._getMinMaxDate(a,"min"),d=this._getMinMaxDate(a,"max");return(!c||b.getTime()>=c.getTime())&&(!d||b.getTime()<=d.getTime())},_getFormatConfig:function(a){var b=this._get(a,"shortYearCutoff");return b=typeof b!="string"?b:(new Date).getFullYear()%100+parseInt(b,10),{shortYearCutoff:b,dayNamesShort:this._get(a,"dayNamesShort"),dayNames:this._get(a,"dayNames"),monthNamesShort:this._get(a,"monthNamesShort"),monthNames:this._get(a,"monthNames")}},_formatDate:function(a,b,c,d){b||(a.currentDay=a.selectedDay,a.currentMonth=a.selectedMonth,a.currentYear=a.selectedYear);var e=b?typeof b=="object"?b:this._daylightSavingAdjust(new Date(d,c,b)):this._daylightSavingAdjust(new Date(a.currentYear,a.currentMonth,a.currentDay));return this.formatDate(this._get(a,"dateFormat"),e,this._getFormatConfig(a))}}),$.fn.datepicker=function(a){if(!this.length)return this;$.datepicker.initialized||($(document).mousedown($.datepicker._checkExternalClick).find("body").append($.datepicker.dpDiv),$.datepicker.initialized=!0);var b=Array.prototype.slice.call(arguments,1);return typeof a!="string"||a!="isDisabled"&&a!="getDate"&&a!="widget"?a=="option"&&arguments.length==2&&typeof arguments[1]=="string"?$.datepicker["_"+a+"Datepicker"].apply($.datepicker,[this[0]].concat(b)):this.each(function(){typeof a=="string"?$.datepicker["_"+a+"Datepicker"].apply($.datepicker,[this].concat(b)):$.datepicker._attachDatepicker(this,a)}):$.datepicker["_"+a+"Datepicker"].apply($.datepicker,[this[0]].concat(b))},$.datepicker=new Datepicker,$.datepicker.initialized=!1,$.datepicker.uuid=(new Date).getTime(),$.datepicker.version="1.8.23",window["DP_jQuery_"+dpuuid]=$})(jQuery);;
jQuery(document).ready(function(){
 
 jQuery(function() {
   
    var start_date = jQuery('.start_date').attr('value');

    if(start_date == '') {
      start_date = new Date();
    }
   
		jQuery('.start_date').datepicker({
			numberOfMonths: 1,
      showOn:"both",
      dateFormat:"dd-mm-yy",
      buttonImageOnly:true,
     	buttonImage: "/media/system/images/calendar.png",
      showButtonPanel: true,
			onSelect: function( selectedDate ) {
				jQuery('.end_date').datepicker( "option", "minDate", selectedDate );
			}
		});
    
		jQuery('.end_date').datepicker({
			numberOfMonths: 1,
      dateFormat:"dd-mm-yy",
      showOn:"both",
      buttonImageOnly:true,
     	buttonImage: "/media/system/images/calendar.png",
      minDate: start_date,
      showButtonPanel: true
		});
	});
})
jQuery(document).ready(function () {

  /*
   * Availability period date picker
   * 
   * 1. When user clicks start date mark the calendar form as 'updated'
   * 2. When user clicks end date (e.g. when date is greater thatn start date) and calendar is edited
   *    2a. Show the update form prepopulated with chosen start and end dates and options to choose availability status
   * 3. User clicks update and ajax request is sent to update the calendar as appropriate
   * 4. Validation (JForm in controller) on server side for date and availability choice
   * 5. Update calendar to reflect chosen dates
   * 6. If user clicks off calendar reset it
   * 7. If user cancels from modal also reset chosen dates
   * 
   */


  jQuery(function () {

    bind_hover();

    jQuery('#availabilityModal').on('hidden', function () {
      // Clear the current selections...
      reset();
    })

    jQuery('.modal').on('shown', function () {

      // Position modal absolute and bump it down to the scrollPosition
      jQuery(this)
              .css({
                position: 'absolute',
                marginTop: jQuery(window).scrollTop() + 'px',
                bottom: 'auto',
                top: '0'
              });

    });



    // Prevent the default click behaviour on the calendar link elements
    jQuery('.avCalendar td a').on('click', function (e) {

      // Prevent the default click behaviour
      e.preventDefault();

      // Get the date that has been clicked...
      var date = jQuery(this).parent('td').attr('data-date');


      // Check if the form has already been updated        
      var form = jQuery('form#adminForm.updated');

      // Existing availability status
      var existing = jQuery(this).parent('td').hasClass('available');


      if (form.length > 0) {

        jQuery(this).addClass('end');


        // TO DO: Check that start and end dates are valid, e.g. end date is after start date



        // Update the start/end date depending on what's been chosen
        jQuery('#adminForm #jform_end_date').attr('value', date);


        // Should have a valid start and end date here...
        jQuery('#availabilityModal').modal('show');

        // TO DO: Disable the save button, show a working spinners (when the save button is pressed)

      } else {

        jQuery(this).addClass('start');

        jQuery('#adminForm #jform_start_date').attr('value', date);

        // Add a 'updated' class to the modal form
        jQuery('form#adminForm').addClass('updated');



        // Need some mechanism to indicate the users intent




      }

      // Rebind the hover event to update the tooltip that is displayed
      bind_hover();
    })
  });


})

/* 
 * Function which resets the current selections
 */
function reset() {

  var start_date = jQuery('#jform_start_date').val('');
  var end_date = jQuery('#jform_end_date').val('');
  var status = jQuery('#jform_availability').val('');
  jQuery('form#adminForm').removeClass('updated');
  jQuery('.avCalendar td a').removeClass('edited start end');
}

/* 
 * Function to bind a tooltip to the hover event on the calendar date picker
 * 
 * 
 */
function bind_hover() {

  // Remove any existing tooltips 
  jQuery('.avCalendar td a').tooltip('destroy');

  // And remove any existing hover events
  jQuery('.avCalendar td a').unbind('mouseenter mouseleave');


  // This adds a tooltip to the date the user is hovering over...
  jQuery('.avCalendar td a').hover(function () {

    // The availability form DOM element
    var form = jQuery('form#adminForm.updated');

    // The start date
    var start_date = jQuery('#jform_start_date').val();

    // The date being hovered over
    var hover_date = jQuery(this).parent().attr('data-date');

    if (form.length > 0) { // Form has already been updated, presumabely with a start date

      var title = Joomla.JText.strings.COM_HELLOWORLD_HELLOWORLD_AVAILABILITY_CHOOSE_END_DATE;

    } else {

      var title = Joomla.JText.strings.COM_HELLOWORLD_HELLOWORLD_AVAILABILITY_CHOOSE_START_DATE;
    }



    // Add the relevant tooltip
    jQuery(this).tooltip({
      placement: 'top',
      title: title,
      trigger: 'hover'
    }).tooltip('show');

    // After creating the tooltip, 

  }, function () { // mouse off call back function
    jQuery(this).tooltip('hide');
  })
}

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
    var latitude = selected.data('latitude'); 
    var longitude = selected.data('longitude');
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
jQuery(document).ready(function () {

  jQuery('.tariffs-container a.delete').on('click', function (e) {
    e.preventDefault();

    if (!confirm(Joomla.JText._('COM_RENTAL_IMAGES_CONFIRM_DELETE_ITEM'))) {
      return false;
    }
    
    // Remove this tariff from the DOM
    jQuery(this).closest('li').remove();
    
    window.onbeforeunload = function() {
      return Joomla.JText._('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    };
    
  })

  jQuery(function () {

    // Select each UL with class of tariff-range and for each 
    jQuery('.tariff-range ol > li').each(function (index) {


      var current = jQuery(this).children().children('input[type=text].tariff_date');
      var start_date_id = '#' + jQuery(current[0]).attr('id');
      var end_date_id = '#' + jQuery(current[1]).attr('id');
      var start_date = jQuery(start_date_id).attr('value');


      if (start_date == '') {
        start_date = new Date();
      }

      jQuery(start_date_id).datepicker({
        numberOfMonths: 1,
        showOn: "both",
        dateFormat: "dd-mm-yy",
        buttonImageOnly: true,
        buttonImage: "/media/system/images/calendar.png",
        showButtonPanel: true,
        onSelect: function (selectedDate) {
          jQuery(end_date_id).datepicker("option", "minDate", selectedDate);
        },
        minDate: new Date()
      });

      jQuery(end_date_id).datepicker({
        numberOfMonths: 1,
        dateFormat: "dd-mm-yy",
        showOn: "both",
        buttonImageOnly: true,
        buttonImage: "/media/system/images/calendar.png",
        onSelect: function (selectedDate) {
          // This is quite strict - disallows setting of a start date after the end date
          //jQuery( "#jform_tariffs_start_date_tariff_0" ).datepicker( "option", "maxDate", selectedDate );
        },
        minDate: start_date,
        showButtonPanel: true
      });
    })
  });
})