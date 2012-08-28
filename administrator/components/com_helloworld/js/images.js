window.addEvent('domready', function(){

  
  SqueezeBox.assign($$('a[rel=woot]'),{
    handler: 'ajax', 
    size: {x: 600, y: 175},
    ajaxOptions: {
      method:'get'
    }
    
  });

	var upload = new Form.Upload('url', {
		onComplete: function(arguments) {   
      // Clear any old messages that may be showing
      $('system-message-container').empty();
      // Create the system-message element which will act as the dl parent
      var system_message = new Element('dl#system-message').setProperty('style', 'display:none');

      // Inject a dt element with a class of errir inside the system-message-container
      var error_dt = new Element('dt.error').appendText('Error');
      var error_dd = new Element('dd.error.message').setProperty('style', 'display:none');  

      system_message.adopt( error_dt, error_dd );

      system_message.inject('system-message-container');

      error_ul = new Element('ul');

      error_dd = error_dd.adopt( error_ul );

      // Inject a dt element with a class of errir inside the system-message-container
      var success_dt = new Element('dt.message').appendText('Message');
      var success_dd = new Element('dd.message.message').setProperty('style', 'display:none');  

      system_message.adopt( success_dt, success_dd );

      system_message.inject('system-message-container');

      success_ul = new Element('ul');

      success_dd = success_dd.adopt( success_ul );
      
      // Decode the files json struct returned from the ajax query...
      var files = JSON.decode( arguments ); // decodes the response into an array

      files.each(function( file ) {
        // Loop over error object and show the error (s)
        if (file.error.length > 0) {
          file.error.each(function( error ){
            var message = new Element('li', {
              text: file.name + ' - ' + error 
            });
            
            error_ul.adopt(message); 
            error_dd.setProperty('style','display:block')

          })
        } else {
          var message = new Element('li', {
            text: file.image_file_name + ' - ' + file.message
          });
          
          // Add the message to the message ul elemet       
          success_ul.adopt(message);     
          success_dd.setProperty('style','display:block')
        }  
      })
      
      system_message.setProperty('style', 'display:block')
      
    }
    
	});
  
  //convert this to MooTools style code?
  try {
 var fileSelect = document.getElementById("droppable"),
    fileElem = document.getElementById("url");

  fileSelect.addEventListener("click", function (e) {
    if (fileElem) {
      fileElem.click();
    }
    e.preventDefault(); // prevent navigation to "#"
  }, false);    
    
  } catch (e) {
    
  }
 






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
