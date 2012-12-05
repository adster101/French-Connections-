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
     	buttonImage: "media/system/images/calendar.png",
      showButtonPanel: true,
			onSelect: function( selectedDate ) {
				jQuery('.end_date').datepicker( "option", "minDate", selectedDate );
			},
      minDate: new Date()
		});
    
		jQuery('.end_date').datepicker({
			numberOfMonths: 1,
      dateFormat:"dd-mm-yy",
      showOn:"both",
      buttonImageOnly:true,
     	buttonImage: "media/system/images/calendar.png",
      minDate: start_date,
      showButtonPanel: true
		});
     
	});
})