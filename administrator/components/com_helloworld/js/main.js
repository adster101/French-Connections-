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
    downloadTemplateId:null,
    //uploadTemplateId:null,
    previewMaxWidth:120,
    previewAsCanvas:false,
    previewMaxHeight:120,
    maxFileSize:2000000,
    singleFileUploads:true, 
    sequentialUploads:true,
    dropZone:dropZone
  }).bind('fileuploadalways', function (e,data){
    //cleanup dropzone
    console.log('Uploads done');
  });
});
// Prevent the default browser drag drop behaviour 
jQuery(function(){
  jQuery(document).bind('drop dragover', function (e) {
    e.preventDefault();
  });  
})