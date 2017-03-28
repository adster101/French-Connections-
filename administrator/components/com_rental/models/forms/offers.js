window.addEvent('domready', function() {	
	// Need to check the dates and availability status is set correctly.
	document.formvalidator.setHandler('offertitle',
		function (value) {
			regex=/[0-9a-zA-Z\s!%*£&.]+$/;
			return regex.test(value);
	});	
  
	document.formvalidator.setHandler('offerdescription',
		function (value) {
      if (value.length < 150) {
    		regex=/[0-9a-zA-Z\s!%*£&.]+$/;
    		return regex.test(value);        
      } else {
        return false;
      } 
	});

	document.formvalidator.setHandler('startdate',
    function (value) {
	
      
      
    	return true;
      
	});

});
