<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$data = JApplication::getUserState('listing', '');
?>

<style>
  .bar {
    height: 18px;
    background: green;
  }
</style>

<?php if (!empty($this->sidebar)): ?>
  <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
  </div>
  <div id="j-main-container" class="span10">
  <?php else : ?>
    <div id="j-main-container">
    <?php endif; ?>
      <!-- The file upload form used as target for the file upload widget -->
      <form class="form-validate" id="fileupload" action="<?php echo JRoute::_('index.php?option=com_helloworld&task=images.upload&' . JSession::getFormToken() . '=1'); ?>" method="GET" enctype="multipart/form-data">
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <noscript><input type="hidden" name="redirect" value="/"></noscript>
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row-fluid fileupload-buttonbar">
          <div class="span7">
            <!-- The fileinput-button span is used to style the file input field as button -->
            <span class="btn btn-success btn-small fileinput-button">
              <i class="icon-plus icon-white"></i>
              <span>Add files...</span>
              <input type="file" name="jform[files]" multiple>
            </span>
            <button type="submit" class="btn btn-primary btn-small start">
              <i class="icon-upload icon-white"></i>
              <span>Start upload</span>
            </button>
            <button type="reset" class="btn btn-warning btn-small cancel">
              <i class="icon-trash icon-white"></i>
              <span>Cancel upload</span>
            </button>
            <button type="button" class="btn btn-danger btn-small delete">
              <i class="icon-trash icon-white"></i>
              <span>Delete</span>
            </button>
            <input type="checkbox" class="toggle">
          </div>
          <!-- The global progress information -->
          <div class="span5 fileupload-progress fade">
            <!-- The global progress bar -->
            <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
              <div class="bar" style="width:0%;"></div>
            </div>
            <!-- The extended global progress information -->
            <div class="progress-extended">&nbsp;</div>
          </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
        <br>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped">
          <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
        </table>
      </form>
      <br>
      <div class="well">
        <h3>Demo Notes</h3>
        <ul>
          <li>The maximum file size for uploads in this demo is <strong>5 MB</strong> (default file size is unlimited).</li>
          <li>Only image files (<strong>JPG, GIF, PNG</strong>) are allowed in this demo (by default there is no file type restriction).</li>
          <li>Uploaded files will be deleted automatically after <strong>5 minutes</strong> (demo setting).</li>
          <li>You can <strong>drag &amp; drop</strong> files from your desktop on this webpage with Google Chrome, Mozilla Firefox and Apple Safari.</li>
          <li>Please refer to the <a href="https://github.com/blueimp/jQuery-File-Upload">project website</a> and <a href="https://github.com/blueimp/jQuery-File-Upload/wiki">documentation</a> for more information.</li>
          <li>Built with Twitter's <a href="http://twitter.github.com/bootstrap/">Bootstrap</a> toolkit and Icons from <a href="http://glyphicons.com/">Glyphicons</a>.</li>
        </ul>
      </div>
    <!-- modal-gallery is the modal dialog used for the image gallery -->
    <div id="modal-gallery" class="modal modal-gallery hide fade" data-filter=":odd" tabindex="-1">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body"><div class="modal-image"></div></div>
      <div class="modal-footer">
        <a class="btn modal-download" target="_blank">
          <i class="icon-download"></i>
          <span>Download</span>
        </a>
        <a class="btn btn-success modal-play modal-slideshow" data-slideshow="5000">
          <i class="icon-play icon-white"></i>
          <span>Slideshow</span>
        </a>
        <a class="btn btn-info modal-prev">
          <i class="icon-arrow-left icon-white"></i>
          <span>Previous</span>
        </a>
        <a class="btn btn-primary modal-next">
          <span>Next</span>
          <i class="icon-arrow-right icon-white"></i>
        </a>
      </div>
    </div>
    <!-- The template to display files available for upload -->
    <script id="template-upload" type="text/x-tmpl">
      {% for (var i=0, file; file=o.files[i]; i++) { %}
      <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
        <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
        <td>
          <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
        </td>
        <td>{% if (!o.options.autoUpload) { %}
          <button class="btn btn-primary start">
            <i class="icon-upload icon-white"></i>
            <span>Start</span>
          </button>
          {% } %}</td>
        {% } else { %}
        <td colspan="2"></td>
        {% } %}
        <td>{% if (!i) { %}
          <button class="btn btn-warning cancel">
            <i class="icon-trash icon-white"></i>
            <span>Cancel</span>
          </button>
          {% } %}</td>
      </tr>
      {% } %}
      </script>
      <!-- The template to display files available for download -->
      <script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">
          {% if (file.error) { %}
          <td></td>
          <td class="name"><span>{%=file.name%}</span></td>
          <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
          <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
          {% } else { %}
          <td class="preview">{% if (file.thumbnail_url) { %}
            <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
          <td class="name">
            <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
          </td>
          <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
          <td colspan="2"></td>
          {% } %}
          <td>
            <button class="btn btn-danger delete" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="icon-trash icon-white"></i>
              <span>Delete</span>
            </button>
            <input type="checkbox" name="delete" value="1" class="toggle">
          </td>
        </tr>
        {% } %}
        </script>
      </div>