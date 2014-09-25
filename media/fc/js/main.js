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
    disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
    previewMaxWidth: 210,
    previewMaxHeight: 120,
    previewCrop: true,
    maxFileSize: 4000000,
    sequentialUploads: true,
    autoUpload: false,
    filesContainer: jQuery('ul.files'),
    uploadTemplateId: null,
    downloadTemplateId: null,
    downloadTemplate: function(o) {
      var rows = jQuery();
      jQuery.each(o.files, function(index, file) {
        var row = jQuery('<li class="template-download fade">' +
                '<span class="preview"></span>' +
                '<p class="name"></p>' +
                (file.error ? '<div class="error"></div>' : '<div class="label label-success"><span class="icon icon-save"> </span> Image successfully uploaded</div>') +
                '</li>');
        if (file.error) {
          row.find('.name').text(file.image_file_name);
          row.find('.error').text(file.error);
        } else {
          row.find('.name').text(file.image_file_name);
          if (file.thumbnail_url) {
            row.find('.preview').append(
                    jQuery('<img>').prop('src', file.thumbnail_url)

                    );
          }
          row.find('a')
                  .attr('data-gallery', '')
                  .prop('href', file.url);

        }
        rows = rows.add(row);
      });
      return rows;

    },
    uploadTemplate: function(o) {
      var rows = jQuery();
      jQuery.each(o.files, function(index, file) {
        var row = jQuery('<li class="template-upload fade clearfix">' +
                '<span class="preview"></span>' +
                '<p><span class="name"></span> (<span class="size"></span>)</p>' +
                '<div class="error"></div>' +
                '<div class="progress progress-success active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar progress-bar-success" style="width:0%;"></div></div>' +
                (!index && !o.options.autoUpload ?
                        '<button class="start btn btn-primary" disabled>Start</button>' : '') +
                (!index ? '<button class="cancel btn btn-warning">Cancel</button>' : '') +
                '</li>');
        row.find('.name').text(file.name);
        row.find('.size').text(o.formatFileSize(file.size));
        if (file.error) {
          row.find('.error').text(file.error);
        }
        rows = rows.add(row);
      });
      return rows;
    }
    // Uncomment the following to send cross-domain cookies:
    //xhrFields: {withCredentials: true},
    //url: 'server/php/'
  }).bind('fileuploaddone', function(e, data) {
    // File has been uploaded, need to refresh the existing images list
    try {

      if (!data.result.files[0].error.length) {

        jQuery("#fc-message")
                .addClass("alert alert-success show")
                .html(data.result.files[0].message);

        var id = jQuery('input[name=id]').val();
        var id = data.result.files[0].version_id;

        jQuery('input[name=id]').val(id);

        jQuery.get(
                "/administrator/index.php?option=com_rental&view=images&layout=default_image_list&format=raw",
                {
                  version_id: id
                })
                .done(function(results) {
          var gallery = jQuery('.image-gallery');
          
          // Update the gallery with the latest list of images
          gallery.empty();
          gallery.html(results);
          
          // Update the review state so that subsequent images don't trigger new versions...
          jQuery('input[name=review]').attr('value', "1");
          
          add_event_handlers();

          var imageCount = jQuery('#imageList').length;

          var span = jQuery('#images > a span:nth-child(2)');

          if (imageCount > 0) {
            span.removeClass('icon-warning');
            span.addClass('icon-ok');
          } else {
            span.removeClass('icon-ok');
            span.addClass('icon-warning');
          }

        });

      } else {

      }
    } catch (err) {
      console.log(err.message);
    }



  });

  // Prevent the default browser drag drop behaviour
  jQuery(document).bind('drop dragover', function(e) {
    e.preventDefault();
  });


  // Add the event handlers to the save caption and delete buttons
  add_event_handlers();

});


// Add the relevant event handlers to the save caption and delete buttons
var add_event_handlers = function() {

  // Add the sortable list to the photo gallery
  var sortableList = new jQuery.JSortableList('#imageList', 'adminForm', '', 'index.php?option=com_rental&task=images.saveOrderAjax&tmpl=component', '', '');

  // Fade out the the uploaded images from the upload queue after five seconds
  jQuery('li.template-download').css('position', 'static').delay(5000).fadeOut(1500);

  // Attach caption counters to the captions
  jQuery('.caption').captionCounter();

  jQuery('.update-caption').updateCaption();

  // Add a confirmation popup to the delete button
  jQuery('.delete').confirmDelete();



};

// Extend the jQuery instance with a few helpful methods...TO DO make into a plugin?
jQuery.fn.extend({
  captionCounter: function() {
    // Update the caption count and what not...
    jQuery(this).each(function() {

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
    });
  },
  updateCaption: function() {

    // Bind a click event to the save caption buttons
    jQuery(this).each(function()
    {

      jQuery(this).on('click', function(event) {

        // Prevent the default click event
        event.preventDefault();

        // Unbind the not save message...
        // window.onbeforeunload = null;

        var that = this;

        // Update the caption via the GET ajax thingamy bob
        var url = jQuery(this).attr('href');
        var defaultValue = jQuery(this).parent().siblings('p').find('input[type=text]').prop('defaultValue');
        var caption = jQuery(this).parent().siblings('p').find('input[type=text]').val();

        jQuery.getJSON(
                url, {
          caption: caption
        }).done(function(data) {

          if (data.error === 1) {
            // There was an error 

          } else {
            // Save and update was okay
            // Update the defaultValue 

            jQuery(that).parent().siblings('p').find('input[type=text]').prop('defaultValue', caption);
          }

          // Update the caption bit with a message
          jQuery(that).parent().siblings('p').find('span.message-container').append(data.message);
          jQuery('span.message').delay(5000).fadeOut(1000);

        });

      });
    });

    // On window un-load check that all captions have been saved
    window.onbeforeunload = function() {
      var changed = false;

      jQuery('#imageList').find('input[type=text]').each(function() {
        var a = jQuery(this).prop('defaultValue');
        var b = jQuery(this).attr('value');
        var equal = a.toUpperCase() === b.toUpperCase();

        if (!equal) {
          changed = true;
        }
      });

      if (changed) {
        return Joomla.JText._('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
      }
    };
  },
  confirmDelete: function() {

    jQuery(this).each(function() {
      jQuery(this).on('click', function(event) {
        if (!confirm(Joomla.JText._('COM_RENTAL_IMAGES_CONFIRM_DELETE_IMAGE'))) {
          event.preventDefault();
        }
        ;
      });
    })

  }

});