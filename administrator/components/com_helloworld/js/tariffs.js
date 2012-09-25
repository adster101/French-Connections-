$(document).ready(function(){
 
 
 
 $(function() {
   
   // Select each UL with class of tariff-range and for each 
   $('.tariff-range').each(function(index){
     var current = $(this).children().children('input[type=text].tariff_date');
     
     var start_date_id = '#'+$(current[0]).attr('id');
     var end_date_id = '#'+$(current[1]).attr('id');
       
     console.log(start_date_id);
     console.log(end_date_id);
     
       
     var start_date = $(start_date_id).attr('value');

   if(start_date == '') {
     start_date = new Date();
   }
   
   
   
		$(start_date_id).datepicker({
			numberOfMonths: 2,
      showOn:"both",
      dateFormat:"dd-mm-yy",
      buttonImageOnly:true,
     	buttonImage: "/media/system/images/calendar.png",
      showButtonPanel: true,
			onSelect: function( selectedDate ) {
				$( end_date_id ).datepicker( "option", "minDate", selectedDate );
			},
      minDate: new Date()
		});
    
		$(end_date_id).datepicker({
			numberOfMonths: 2,
      dateFormat:"dd-mm-yy",
      showOn:"both",
      buttonImageOnly:true,
     	buttonImage: "/media/system/images/calendar.png",
			onSelect: function( selectedDate ) {
				// This is quite strict - disallows setting of a start date after the end date
        //$( "#jform_tariffs_start_date_tariff_0" ).datepicker( "option", "maxDate", selectedDate );
			},
      minDate: start_date,
      showButtonPanel: true

      
		});
     
     
   })   
   
   
   

	});
       
 
})