/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

(function ($) {
  $.JSortableList = function (tableWrapper, formId, sortDir, saveOrderingUrl, options, nestedList) {
    $(tableWrapper).sortable({
      placeholder: "ui-state-highlight",
      forcePlaceholderSize: true,
      helper: "clone",
      opacity: 0.8,
      stop: function (e, ui) {
        //serialize form then post to callback url
        var formData = $('#' + formId).serialize();
        formData = formData.replace('task', '');
        formData = formData + '&' + $(this).sortable('serialize');
        $.post(saveOrderingUrl, formData);

        $('#imageList .thumbnail-default').empty();
        $('#imageList li:nth-child(1) .thumbnail-default').html('<span class="icon-default pull-left">&nbsp;</span>');
        
        var messages = {
          "success": ["<span class='icon-publish'></span>Image re-ordering was successfully saved"],
        };
        Joomla.renderMessages(messages);

        setTimeout(function(){
          $( "#system-message-container" ).fadeOut( "slow" );          
        },
        2000)

        
        
      }
    });
  }
})(jQuery);
