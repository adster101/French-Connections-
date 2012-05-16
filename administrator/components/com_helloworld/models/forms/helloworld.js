window.addEvent('domready', function() {
	document.formvalidator.setHandler('greeting',
		function (value) {
			regex=/^[^0-9]+$/;
			return regex.test(value);
	});
	
	document.formvalidator.setHandler('occupancy',
		function (value) {
			regex=/^[^a-z]+$/;
			return regex.test(value);
	});	
	document.formvalidator.setHandler('swimming',
		function (value) {
			regex=/^[0-1]+$/;
			return regex.test(value);
	});	
});
