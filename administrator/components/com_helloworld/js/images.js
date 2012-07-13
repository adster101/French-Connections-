window.addEvent('domready', function(){

	var upload = new Form.Upload('url', {
		onComplete: function(arguments){
      //alert('Completed uploading the Files');
      //window.location.reload();
      console.log(arguments);
      // Need to take the list of arguments and append this to the gallery/library view
    }
	});
  
  //convert this to MooTools style code?
  var fileSelect = document.getElementById("droppable"),
    fileElem = document.getElementById("url");

  fileSelect.addEventListener("click", function (e) {
    if (fileElem) {
      fileElem.click();
    }
    e.preventDefault(); // prevent navigation to "#"
  }, false);






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
