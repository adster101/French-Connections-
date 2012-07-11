window.addEvent('domready', function(){

	var upload = new Form.Upload('url', {
		onComplete: function(){
      //alert('Completed uploading the Files');
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
