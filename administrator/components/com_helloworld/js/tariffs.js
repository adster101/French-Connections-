$(document).ready(function(){
 
 
 
 $(function() {
   
   var start_date = $('#jform_tariffs_start_date_tariff_0').attr('value');
   
   if(start_date == '') {
     start_date = new Date();
   }
   
		$( "#jform_tariffs_start_date_tariff_0" ).datepicker({
			numberOfMonths: 1,
      showOn:"both",
      dateFormat:"dd-mm-yy",
      buttonImageOnly:true,
     	buttonImage: "/media/system/images/calendar.png",
      showButtonPanel: true,
			onSelect: function( selectedDate ) {
				$( "#jform_tariffs_end_date_tariff_0" ).datepicker( "option", "minDate", selectedDate );
			},
      minDate: start_date
		});
    
		$( "#jform_tariffs_end_date_tariff_0" ).datepicker({
			numberOfMonths: 1,
      dateFormat:"dd-mm-yy",
      showOn:"both",
      buttonImageOnly:true,
     	buttonImage: "/media/system/images/calendar.png",
			onSelect: function( selectedDate ) {
				// This is quite strict - disallows setting of a start date after the end date
        //$( "#jform_tariffs_start_date_tariff_0" ).datepicker( "option", "maxDate", selectedDate );
			},
      minDate: start_date
      
		});
	});
       
 
})