jQuery(document).ready(function() {



  jQuery('.shortlist-login').on('click', function(event) {

    event.preventDefault();
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
  


  jQuery('body').on('click', '.popover span', function(ev) { // When a pop over span is clicked
    var el = jQuery(this);
    var favourite = el.parent().parent().siblings('a');
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


  jQuery(function() {

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
      },
      minDate: new Date()
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
  });

  if (jQuery('.hasdatepicker').length) {
    jQuery(".hasdatepicker").datepicker({dateFormat: 'yy-mm-dd'});
  };

  if (jQuery("#contactDetails").length) {

    var checked = jQuery('input[name="jform[use_invoice_details]"');

    show_contact(checked);

    jQuery("input[name='jform[use_invoice_details]']").on('click', function(e) {

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
    window.onbeforeunload = youSure;
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
            window.onbeforeunload = youSure;
          }
        });
      });
    }
  } catch (e) {
    // what to do!?
  }


});

var youSure = function() {
  return Joomla.JText._('COM_HELLOWORLD_HELLOWORLD_UNSAVED_CHANGES');

}

var getContent = function(that) {

  action = jQuery(that).data('action');

  if (action == 'remove') {
    return "<span class=\'click icon icon-checkbox\'>&nbsp;Shortlist</span><hr /><a href=\'/shortlist\'>View shortlist</a>";

  }
  return "<span class=\'click icon icon-checkbox-unchecked\'>&nbsp;Shortlist</span><hr /><a href=\'/shortlist\'>View shortlist</a>";


}

var checkEditor = function(elements, index, array) {
  console.log(index);
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


// Fires on occasion when a button has it bound to it's onclick event
Joomla.submitbutton = function(task)
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
      alert(Joomla.JText._('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE', ''));
      return false;
    }
  }
}


  