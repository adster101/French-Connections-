window.addEvent('domready', function(){

	var upload = new Form.Upload('url', {
		onComplete: function(arguments){
      //alert('Completed uploading the Files');
      //window.location.reload();
      var systemMessage = new Element('dl#system-message').inject('system-message-container');
      
      var messageList = new Element('ul#message').inject('system-message');
      
      // Decode the files json struct returned from the ajax query...
      var files = JSON.decode( arguments ); // decodes the response into an array
      
      files.each(function( file ) {
        // Loop over error object and show the error (s)
        file.error.each(function( error ){
          var message = new Element('li', {
            text: error } ).inject('message');
        })
      })
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
  

  new Sortables('#library, #gallery', {
      clone:true,
      revert:true,
      opacity:0.7,
      handle:'.handle',
      
    /* once an item is selected */
    onStart: function(el) { 
      
    },
    onComplete: function(el) {
      // Need to add a notice to inform user changes have been made and not saved...
      // Can we trigger an onChange event for the form at the same time?
      
      // Determine what is happening here. Scenarios are as follows
      // 1. No library exists and just sorting between gallery images
      // 2. Image is just sorted within either a library or gallery
      // 3. Image is moved between gallery and library and vice versa
      
      // Image area the drop target (e.g. gallery or library)
      image_drop_area = '';
      // The input fields we are interested in 
      inputs = '';
      
      
      
      image_drop_area = el.getParent('ul').getProperty('id');
      
      
      // Get the input values for this li item
      inputs = el.getElements('input');
      
      inputs.each(function(e){
        element_name = e.getProperty('name');
        
        if (element_name.indexOf(image_drop_area) == -1) {
          if (image_drop_area == 'gallery') {
            new_element_name = element_name.replace("[library", "[gallery"); 
            e.setProperty('name', new_element_name)
          } else {
            new_element_name = element_name.replace('gallery', 'library'); 
            e.setProperty('name', new_element_name)
          }
        }
      });


      
      
    }
    
  });
});
