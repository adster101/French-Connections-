window.addEvent('domready', function(){ 
  
  SqueezeBox.assign($$('[rel=woot]'),{
    handler: 'ajax', 
    size: {x: 600, y: 175},
    ajaxOptions: {
      method:'get'
    }  
  });

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
            
            console.log($$('library'));
            
          } else {
            new_element_name = element_name.replace('gallery', 'library'); 
            e.setProperty('name', new_element_name)
          }
        }
      });
      
      
      
      // This triggers the onchange event for the gallery/library form
      $$('form.form-validate')[0].fireEvent('change');

      
      
    }
    
  });
  
  uploadImage();
  
});

function uploadImage() {
	var upload = new Form.Upload('jform_upload_images', {
		onComplete: function(arguments) {   
      // Clear any old messages that may be showing
      $('system-message').empty();
      
      // Create the system-message element which will act as the dl parent
      var error_div = new Element
        ('div.alert.alert-error').grab(
          new Element('a',{text:'x', class:'close'}).setProperty('data-dismiss','alert')
        ).grab(
          new Element('h4', {text:'Message'})
        );
 
      // Create the system-message element which will act as the dl parent
      var success_div = new Element
        ('div.alert.alert-success').grab(
          new Element('a',{text:'x', class:'close'}).setProperty('data-dismiss','alert')
        ).grab(
          new Element('h4', {text:'Message'})
        );     
          
      $('system-message').adopt( error_div );
      $('system-message').adopt( success_div );

      var error_detail = new Element('div');

      var success_detail = new Element('div');

      error_div = error_div.adopt( error_detail );
      success_div = success_div.adopt( success_detail );

      
      // Decode the files json struct returned from the ajax query...
      var files = JSON.decode( arguments ); // decodes the response into an array

      files.each(function( file ) {
        // Loop over error object and show the error (s)
        if (file.error.length > 0) {
          file.error.each(function( error ){
            var message = new Element('p', {
              text: file.name + ' - ' + error 
            });
            
            error_detail.adopt(message); 

          })
        } else {
          var message = new Element('p', {
            text: file.image_file_name + ' - ' + file.message
          });
          
          // Add the message to the message ul elemet       
          success_detail.adopt(message); 

        }  
      })
      
   }
    
	});
  
  //convert this to MooTools style code?
  try {
  var fileSelect = document.getElementById("explore"),
    fileElem = document.getElementById("jform_upload_images");

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
  


  
}