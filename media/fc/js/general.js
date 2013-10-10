jQuery(document).ready(function() {


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

  if (window.tinymce) {
    tinymce.onAddEditor.add(function(sender, editor) {
      editor.onKeyUp.add(function(editor, event) {
        if (editor.isDirty())
          window.onbeforeunload = youSure;
      });
      editor.onChange.add(function(editor) {
        if (editor.isDirty() && !$('body').data('isSaving'))
          window.onbeforeunload = youSure;
        ;
      });
    });
  }


});

var youSure = function() {
  return Joomla.JText._('COM_HELLOWORLD_HELLOWORLD_UNSAVED_CHANGES');

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


  