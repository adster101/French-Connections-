window.addEvent('domready', function(){

	var upload = new Form.Upload('url', {
		onComplete: function(){
			alert('Completed uploading the Files');
		}
	});

	if (!upload.isModern()){
		// Use something like
	}

});
