/*
 * jQuery File Upload Plugin JS Example 7.0
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */
 


jQuery(function () {
  
  'use strict';



  // Initialize the jQuery File Upload widget:
  jQuery('#fileupload').fileupload({
    // Uncomment the following to send cross-domain cookies:
    //xhrFields: {withCredentials: true},
    //url: 'index.php?option=com_helloworld&task=images.upload'
    //downloadTemplateId:null,
    //uploadTemplateId:null,
    previewMaxWidth:230,
    previewAsCanvas:false,
    previewMaxHeight:120,
    maxFileSize:4000000,
    singleFileUploads:true, 
    sequentialUploads:true,
    dropZone:dropZone
  }).bind('fileuploaddone', function (e,data){
    
                
    
    
    
    // File has been uploaded, need to refresh the existing images list
    try {
      
      if (!data.result.files[0].error.length) {
                        
                            jQuery("#fc-message")
                              .addClass("alert alert-success show")
                              .html(data.result.files[0].message);

                            // Will be the place to alert about submitting for approval etc
                        
                        
                       
                   
                       
        
        
        // Empty the exisiting image list
        // Show a spinner bar
        // Get the new list
        // Show the new list
        // Hide the spinner bar
        
        var property_id = data.result.files[0].property_id;
        
        jQuery.get(
          "/administrator/index.php?option=com_helloworld&view=images&layout=default_image_list&format=raw",
          {
            id:property_id
          })
        .done(function(data) {
          jQuery('.ui-sortable').empty();
          jQuery('.ui-sortable').html(data);
        });
      } else {
        
      } 
    } catch(err) {
      console.log(err.message);
    }

    
   
    
    
    
    
  }).bind('fileuploadadd', function (e,data){
    
    
    });
});
// Prevent the default browser drag drop behaviour 
jQuery(function(){
  jQuery(document).bind('drop dragover', function (e) {
    e.preventDefault();
  });  
  
  jQuery('.delete').on('click', function(event) {
    
    
    if (!confirm("Really delete?")) {  event.preventDefault() };
      
    
    
  })
  
})