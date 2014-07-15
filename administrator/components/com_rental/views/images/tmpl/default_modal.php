<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="imageUploadModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Upload pics baby</h3>
  </div>
  <div class="modal-body">
    <!-- The file upload form used as target for the file upload widget -->
    <form id="fileupload" action="<?php echo JRoute::_('index.php?option=com_rental&task=images.upload&' . JSession::getFormToken() . '=1') ?>" method="POST" enctype="multipart/form-data">
      <!-- Redirect browsers with JavaScript disabled to the origin page -->
      <!-- Redirect browsers with JavaScript disabled to the origin page -->
      <noscript><input type="hidden" name="redirect" value="http://blueimp.github.io/jQuery-File-Upload/"></noscript>
      <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
      <div class="fileupload-buttonbar">
        <!-- The fileinput-button span is used to style the file input field as button -->
        <span class="btn btn-success fileinput-button">
          <i class="glyphicon glyphicon-plus"></i>
          <span>Add files...</span>
          <input type="file" name="jform[files]" multiple>
        </span>
        <button type="submit" class="btn btn-primary start">
          <i class="glyphicon glyphicon-upload"></i>
          <span>Start upload</span>
        </button>
        <button type="reset" class="btn btn-warning cancel">
          <i class="glyphicon glyphicon-ban-circle"></i>
          <span>Cancel upload</span>
        </button>
        <!-- The global file processing state -->
        <span class="fileupload-process"></span>
        <!-- The global progress state -->
        <div class="fileupload-progress fade">
          <!-- The global progress bar -->
          <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
          </div>
        </div>
      </div>
      <!-- The table listing the files available for upload/download -->
      <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
      <input type="hidden" name="review" value="<?php echo $this->unit->review ?>" />
      <input type="hidden" name="id" value="<?php echo $this->unit->id ?>" />
      <input type="hidden" name="property_id" value="<?php echo $this->unit->property_id ?>" />
      <input type="hidden" name="unit_id" value="<?php echo $this->unit->unit_id ?>" />
    </form>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
  {% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class="template-upload fade">
  <td>
  <span class="preview"></span>
  </td>
  <td>
  <p class="name">{%=file.name%}</p>
  <strong class="error text-danger"></strong>
  </td>
  <td>
  <p class="size">Processing...</p>
  <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
  </td>
  <td>
  {% if (!i && !o.options.autoUpload) { %}
  <button class="btn btn-primary start" disabled>
  <i class="glyphicon glyphicon-upload"></i>
  <span>Start</span>
  </button>
  {% } %}
  {% if (!i) { %}
  <button class="btn btn-warning cancel">
  <i class="glyphicon glyphicon-ban-circle"></i>
  <span>Cancel</span>
  </button>
  {% } %}
  </td>
  </tr>
  {% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
  {% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class="template-download fade">
  <td>
  <span class="preview">
  {% if (file.thumbnailUrl) { %}
  <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
  {% } %}
  </span>
  </td>
  <td>
  <p class="name">
  {% if (file.url) { %}
  <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
  {% } else { %}
  <span>{%=file.name%}</span>
  {% } %}
  </p>
  {% if (file.error) { %}
  <div><span class="label label-danger">Error</span> {%=file.error%}</div>
  {% } %}
  </td>
  <td>
  <span class="size">{%=o.formatFileSize(file.size)%}</span>
  </td>
  <td>
  {% if (file.deleteUrl) { %}
  <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
  <i class="glyphicon glyphicon-trash"></i>
  <span>Delete</span>
  </button>
  <input type="checkbox" name="delete" value="1" class="toggle">
  {% } else { %}
  <button class="btn btn-warning cancel">
  <i class="glyphicon glyphicon-ban-circle"></i>
  <span>Cancel</span>
  </button>
  {% } %}
  </td>
  </tr>
  {% } %}
</script>