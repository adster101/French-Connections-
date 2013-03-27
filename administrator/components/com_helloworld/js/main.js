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
(function (factory) {
  'use strict';
  if (typeof define === 'function' && define.amd) {
    // Register as an anonymous AMD module:
    define([
      'jquery',
      'tmpl',
      'load-image',
      './jquery.fileupload-fp'
      ], factory);
  } else {
    // Browser globals:
    factory(
      window.jQuery,
      window.tmpl,
      window.loadImage
      );
  }
}(function ($, tmpl, loadImage) {
  'use strict';

  // Override the _startHandler method so that the start button is not disabled if the caption is empty
  $.widget('blueimp.fileupload', $.blueimp.fileupload, {
    _startHandler: function (e) {
      e.preventDefault();

      var button = $(e.currentTarget),
      template = button.closest('.template-upload'),
      data = template.data('data');
      if (data && data.submit && !data.jqXHR && data.submit()) {
        button.prop('disabled', true);
      }
    }

  });

}));

jQuery(function () {
  
  'use strict';

  // Initialize the jQuery File Upload widget:
  jQuery('#fileupload').fileupload({
    // Uncomment the following to send cross-domain cookies:
    //xhrFields: {withCredentials: true},
    //url: 'index.php?option=com_helloworld&task=images.upload'
    //downloadTemplateId:null,
    //uploadTemplateId:null,
    maxFileSize:2000000,
    singleFileUploads:true, 
  }).bind('fileuploadsubmit', function (e, data) {
    
    var inputs = data.context.find(':input');
    var labels = data.context.find('label');

    if (inputs.filter('[required][value=""]').first().focus().length) {
      jQuery(inputs.filter('[required]')).addClass('invalid');
      jQuery(labels).addClass('invalid');
      
      return false;
    } 
    
    data.formData = inputs.serializeArray();
  });
});


