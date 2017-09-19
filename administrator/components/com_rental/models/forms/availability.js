window.addEvent('domready', function() {	
	// Need to check the dates and availability status is set correctly.
	document.formvalidator.setHandler('availability',
		function (value) {
			regex=/^[0-6]+$/;
			return regex.test(value);
	});	
});
