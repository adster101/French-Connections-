window.addEvent('domready', function(){

	var upload = new Form.Upload('url', {
		onComplete: function(arguments){
      //alert('Completed uploading the Files');
      //window.location.reload();
      //console.log(arguments);
      // Need to take the list of arguments and append this to the gallery/library view
      console.log(this);
    }
	});

	if (!upload.isModern()){
		// Use something like
	}
  
  var draggable = $('draggable-image-list');

  new Sortables(draggable, {
      clone:true,
      revert:true,
      opacity:0.7,
      handle:'.handle'
  });
});
