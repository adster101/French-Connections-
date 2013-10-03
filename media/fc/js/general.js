jQuery(document).ready(function() {

  var vatID = jQuery("#jform_vat_status")[0].value;
  console.log(vatID);
  show_vat(vatID);

  jQuery("#jform_vat_status").change(function(e) {

    // Get the selected vat_aid_id code
    var vatID = jQuery(this)[0].value;
    show_vat(vatID);

  });

 
});

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


      // Need to remove any hidden fields from COMPANYNUMBER field that make it required.
      company_number.hide();

    } else {

      company_number.hide();
      vat_number.hide();

      toggle('#jform_vat_number', false);
      toggle('#jform_company_number', false);
    }

  };

  var toggle = function(elem, show) {

    field = jQuery(elem);
    console.log(field);
    if (show) {
      field.addClass('required');
      field.attr('required', 'required');
    } else {
      field.removeClass('required');
      field.removeAttr('required');
      field.val('');
    }


  }

window.addEvent('domready', function() {

  document.formvalidator.setHandler('name',
          function(value) {
            regex = /^[a-zA-Z]+$/;
            console.log(regex.test(value));
            return regex.test(value);
          });
  document.formvalidator.setHandler('telephone',
          function(value) {
            // Only allow digits, spaces and pluses
            regex = /^[\d +]{11,25}$/;
            return regex.test(value);
          });
  document.formvalidator.setHandler('message',
          function(value) {
            regex = /^[\w-\/., !"'\n]+$/;
            return regex.test(value);
          });
  document.formvalidator.setHandler('date',
          function(value) {
            regex = /^(\d{4})-(\d{2})-(\d{2})$/;
            return regex.test(value);
          });
  document.formvalidator.setHandler('numeric',
          function(value) {
            regex = /^[0-9]{1,2}/;
            return regex.test(value);
          });


});


  