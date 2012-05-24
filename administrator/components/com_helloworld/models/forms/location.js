window.addEvent('domready', function() {	
	document.formvalidator.setHandler('latitude',
		function (value) {
			regex=/^[0-9.-]+$/;
			return regex.test(value);
	});	
	document.formvalidator.setHandler('longitude',
		function (value) {
			regex=/^[0-9.-]+$/;
			return regex.test(value);
	});	
	document.formvalidator.setHandler('nearest_town',
		function (value) {
			regex=/^[a-zA-Z\s]+$/;
			return regex.test(value);
	});	
	document.formvalidator.setHandler('distance_to_coast',
		function (value) {
			regex=/^[0-9]+$/;
			return regex.test(value);
	});	
	
});
