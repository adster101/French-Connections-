/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

(function($) {
  $.JSortableList = function(tableWrapper, formId, sortDir, saveOrderingUrl, options, nestedList) {
    $(tableWrapper).sortable({
      placeholder: "ui-state-highlight",
      forcePlaceholderSize : true,
      helper:"clone",
      stop: function(e, ui) {
        //serialize form then post to callback url
        var formData = $('#' + formId).serialize();
        formData = formData.replace('task', '');
        formData = formData + '&' + $(this).sortable('serialize');
        $.post(saveOrderingUrl, formData);
        
        $('#imageList .thumbnail-default').empty();
        $('#imageList li:nth-child(1) .thumbnail-default').html('<span class="icon-default pull-left">&nbsp;</span>');
        
      }
    });
  }
})(jQuery);
