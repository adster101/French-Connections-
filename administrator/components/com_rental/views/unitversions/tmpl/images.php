<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = true;

if ($saveOrder) {
  $saveOrderingUrl = 'index.php?option=com_rental&task=images.saveOrderAjax&tmpl=component';
  JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$app = JFactory::getApplication();

$input = $app->input;

$unit_id = $input->get('unit_id', '', 'int');
$data = array('item' => $this->unit, 'progress' => $this->progress);
?>

<div class="row-fluid">

  <?php if (!empty($this->sidebar)): ?>

    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span8">
    <?php else : ?>
      <div id="j-main-container" class="span10">
      <?php endif; ?>
      <?php
      $progress = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
      echo $progress->render($data);

      $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
      echo $layout->render($data);
      ?>
      <div id="collapseUpload" class="in collapse">
        <!-- The file upload form used as target for the file upload widget -->
        <form class="form-validate" id="fileupload" action="<?php echo JRoute::_('index.php?option=com_rental&task=images.upload&' . JSession::getFormToken() . '=1&property_id=' . (int) $this->unit->property_id . '&version_id=' . (int) $this->unit->id . '&unit_id=' . $this->unit->unit_id); ?>" method="GET" enctype="multipart/form-data">
          <!-- Redirect browsers with JavaScript disabled to the origin page -->
          <noscript><input type="hidden" name="redirect" value="/"></noscript>
          <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
          <fieldset>
            <legend>
              <?php echo JText::_('COM_RENTAL_IMAGES_UPLOAD_IMAGES'); ?>
            </legend>
            <div class="row-fluid fileupload-buttonbar">
              <div class="span7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                  <i class="icon-plus icon-white"></i>
                  <span>Add files to upload</span>
                  <input type="file" name="jform[files]" multiple>
                </span> <button type="submit" class="btn btn-primary start">
                  <i class="icon-upload icon-white"></i>
                  <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                  <i class="icon-trash icon-white"></i>
                  <span>Cancel upload</span>
                </button>
                <!--<button type="button" class="btn btn-danger delete">
                  <i class="icon-trash icon-white"></i>
                  <span>Delete</span>
                </button>
                <input type="checkbox" class="toggle">-->
              </div>
              <!-- The global progress information -->
              <div class="span5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                  <div class="bar" style="width:0%;"></div>
                </div>
              </div>

              <div class="row-fluid">
                <div id="dropZone" class="well span12" style="min-height:125px;border:dashed 3px">
                  <!-- The loading indicator is shown during file processing -->
                  <div class="fileupload-loading"></div>
                  <!-- The table listing the files available for upload/download -->
                  <table role="presentation" class="table">
                    <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
                  </table>
                </div>

              </div>
            </div>
          </fieldset>
                        <input type="hidden" name="parent_id" value="<?php echo $this->unit->property_id; ?>" />

        </form>

      </div>

      <!-- The template to display files available for upload -->
      <script id="template-upload" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-upload fade">
          <td class="preview">
            <span class="fade"></span>
          </td>
          <td class="name"><span>{%=file.name%}</span> - <span>{%=o.formatFileSize(file.size)%}</span></td>

          {% if (file.error) { %}
          <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
          {% } else if (o.files.valid && !i) { %}
          <td>
            <div class="progress progress-success progress-striped active" style="margin-bottom:0" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
          </td>
          <td class="start">{% if (!o.options.autoUpload) { %}
            <button class="btn btn-primary start">
              <i class="icon-upload icon-white"></i>
              <span>Start</span>
            </button>
            {% } %}</td>
          {% } else { %}
          <td colspan="2">

          </td>
          {% } %}

          <td class="cancel">{% if (!i) { %}
            <button class="close pull-right">
              &times;
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
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
            {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
              <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
              {% } %}</td>
            <td class="notice" colspan="2"><span class="label label-success">Success!</span> {%=file.message%}</td>


            {% } %}

          </tr>
          {% } %}
          </script>

          <form action="<?php echo JRoute::_('index.php?option=com_rental&view=images&unit_id=' . (int) $unit_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

            <fieldset>
              <legend>
                <?php echo JText::_('COM_RENTAL_IMAGES_EXISTING_IMAGE_LIST'); ?>
              </legend>
              <div class="pull-left">
                <?php echo $this->pagination->getListFooter() ?>
              </div>
              <div class="btn-group pull-right">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
              </div>
              <table id="articleList" class="table table-striped">
                <thead>
                  <tr>
                    <th class="nowrap  hidden-phone">
                      <?php echo JText::_('COM_RENTAL_HELLOWORLD_IMAGE_ORDERING'); ?>
                    </th>
                    <th>
                      <?php echo JText::_('COM_RENTAL_IMAGES_CHOOSE_THUMBNAIL'); ?>
                    </th>
                    <th>
                      <?php echo JText::_('COM_RENTAL_HELLOWORLD_IMAGES_THUMBNAIL'); ?>
                    </th>
                    <th>
                      <?php echo JText::_('COM_RENTAL_HELLOWORLD_IMAGES_CAPTION'); ?>
                    </th>

                    <th>
                      <?php echo JText::_('COM_RENTAL_HELLOWORLD_DELETE_IMAGE'); ?>
                    </th>
                  </tr>
                </thead>
                <tbody class="ui-sortable">
                  <?php echo $this->loadTemplate('image_list'); ?>
                </tbody>




                <input type="hidden" name="extension" value="<?php echo 'com_rental'; ?>" />


              </table>
              <input type="hidden" name="task" value="" />
              <input type="hidden" name="boxchecked" value="0" />
              <?php echo JHtml::_('form.token'); ?>

            </fieldset>
          </form>
        </div>

        <div class="span2">
          <div class="well well-small">
            <h3>Image upload notes</h3>
            <ul>
              <li>The maximum file size for upload is <strong>2 MB</strong>.</li>
              <li>Only image files (<strong>JPG, GIF, PNG</strong>) are allowed in this demo (by default there is no file type restriction).</li>
              <li>You can <strong>drag &amp; drop</strong> files from your desktop on this webpage with Google Chrome, Mozilla Firefox and Apple Safari.</li>
              <li>Image queued for upload will appear to the list on the left.</li>
            </ul>
          </div>
        </div>

      </div>
    </div>

