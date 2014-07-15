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

jQuery(function() {

  'use strict';
    // Initialize the jQuery File Upload widget:
    jQuery('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        //url: 'server/php/'
    });

   
      // Load existing files:
        jQuery('#fileupload').addClass('fileupload-processing').fileupload('add', function(){
          alert("eoot");
        });
        jQuery.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: jQuery('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: jQuery('#fileupload')[0]
        }).always(function () {
            jQuery(this).removeClass('fileupload-processing');
        }).done(function (result) {
            jQuery(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });
});

// When the Document is ready...
jQuery(function() {

  // Prevent the default browser drag drop behaviour
  jQuery(document).bind('drop dragover', function(e) {
    e.preventDefault();
  });

  // Add the event handlers to the save caption and delete buttons
  add_event_handlers();

});

// Add the relevant event handlers to the save caption and delete buttons
function add_event_handlers() {
  var sortableList = new jQuery.JSortableList('#imageList', 'adminForm', '', 'index.php?option=com_rental&task=images.saveOrderAjax&tmpl=component', '', '');

  jQuery('.delete').on('click', function(event) {
    if (!confirm("Really delete?")) {
      event.preventDefault();
    }
    ;
  });

  // Update the caption count and what not...
  jQuery('.caption').each(function() {

    var that = this;
    var length = jQuery(that).find('input').val().length;
    var input = jQuery(that).find('input[type=text]');

    // Update the span element with the initial value of the caption
    jQuery(that).find('span.caption-count').text(75 - length);

    jQuery(input).on('keyup', function(event) {

      // On the keyup event, update the value of the span count element
      var length = jQuery(that).find('input').val().length;

      jQuery(that).find('span.caption-count').text(75 - length);

    });
  })

  jQuery('td.caption input').change(function() {
    window.onbeforeunload = function() {
      return Joomla.JText._('COM_HELLOWORLD_HELLOWORLD_UNSAVED_CHANGES');
    };
  });
  // Remove the uploaded images from the queue
  jQuery('tr.template-download').css('position', 'static').delay(5000).fadeOut(1500);

  // Bind a click event to the update-caption buttons
  jQuery('.update-caption').each(function() {

    jQuery(this).on('click', function(event) {

      event.preventDefault();
      // Unbind the not save message...
      window.onbeforeunload = null;

      var that = this;

      // Update the caption via the GET ajax thingamy bob
      var url = jQuery(this).attr('href');
      var caption = jQuery(this).siblings('input[type=text]').val();

      jQuery.get(
              url, {
        caption: caption
      })
              .done(function(data) {
        // Update the caption bit with a message
        jQuery(that).siblings('p').append(data);
        jQuery('span.message').delay(5000).fadeOut(1000);

      });

    })
  })

}