$(document).ready(function(){
 
 
 
 $(function() {
		$( "#jform_tariffs_start_date_tariff_0" ).datepicker({
			numberOfMonths: 3,
      minDate:-0,
      showOn:"both",
      dateFormat:"dd-mm-yy",
      buttonImageOnly:true,
     	buttonImage: "/media/system/images/calendar.png",

			onSelect: function( selectedDate ) {
				$( "#jform_tariffs_end_date_tariff_0" ).datepicker( "option", "minDate", selectedDate );
			}
		});
    
		$( "#jform_tariffs_end_date_tariff_0" ).datepicker({
			numberOfMonths: 3,
      minDate:-0,
      dateFormat:"dd-mm-yy",
      showOn:"both",
      buttonImageOnly:true,
     	buttonImage: "/media/system/images/calendar.png",
			onSelect: function( selectedDate ) {
				$( "#jform_tariffs_start_date_tariff_0" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	});
 
 
})