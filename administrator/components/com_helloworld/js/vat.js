/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function(){

	if(jQuery('#jform_vat_vat_status').length) {
		// It does so bind an on change event to it.
		jQuery("select#jform_vat_vat_status").change(function() {
			// Get the selected vat_aid_id code
			var vatID = jQuery('#jform_vat_vat_status')[0].value;
			// If vatID is za then show the siret number field and add relevant hidden fields to make this a required field ( on server side ).
      console.log(vatID);
			if (vatID == 'za') {
				// Display the Company number field
				jQuery('#').show();
				// Need to remove any hidden fields from VATNUMBER field that make it required.
				jQuery('# input[type="hidden"]').remove();
				// And lastly hide the VAT field if it's being displayed! 
				jQuery('#').hide();	
        // If the vatID is ECS then show the VAT/TVA number
			} else if (vatID == 'ECS') {
				// Display the Company number field
				jQuery('#vat_number').show();	
			} else {
        // Also make both not required
				jQuery('#vat_number').hide();	
				jQuery('#company_number').hide();					
			}
		});
	}
})
