jQuery(document).ready(function(){
 
 
 jQuery(function() {
   
   // Select each UL with class of tariff-range and for each 
   jQuery('.tariff-range').each(function(index){
     var current = jQuery(this).children().children('input[type=text].tariff_date');  
     var start_date_id = '#'+jQuery(current[0]).attr('id');
     var end_date_id = '#'+jQuery(current[1]).attr('id');    
     var start_date = jQuery(start_date_id).attr('value');

   if(start_date == '') {
     start_date = new Date();
   }
   
		jQuery(start_date_id).datepicker({
			numberOfMonths: 2,
      showOn:"both",
      dateFormat:"dd-mm-yy",
      buttonImageOnly:true,
     	buttonImage: "/media/system/images/calendar.png",
      showButtonPanel: true,
			onSelect: function( selectedDate ) {
				jQuery( end_date_id ).datepicker( "option", "minDate", selectedDate );
			},
      minDate: new Date()
		});
    
		jQuery(end_date_id).datepicker({
			numberOfMonths: 2,
      dateFormat:"dd-mm-yy",
      showOn:"both",
      buttonImageOnly:true,
     	buttonImage: "/media/system/images/calendar.png",
			onSelect: function( selectedDate ) {
				// This is quite strict - disallows setting of a start date after the end date
        //jQuery( "#jform_tariffs_start_date_tariff_0" ).datepicker( "option", "maxDate", selectedDate );
			},
      minDate: start_date,
      showButtonPanel: true
		});
   })   
	});
})