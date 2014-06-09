<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = true;

if ($saveOrder) {
  $saveOrderingUrl = 'index.php?option=com_rental&task=images.saveOrderAjax&tmpl=component';
  JHtml::_('fcsortablelist.fcsortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false);
}

$app = JFactory::getApplication();

$input = $app->input;

$unit_id = $input->get('unit_id', '', 'int');
$data = array('progress' => $this->progress);
?>

<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span8">
    <?php else : ?>
      <div id="j-main-container" class="span12">
      <?php endif; ?>
      <?php
      $progress = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
      echo $progress->render($data);

      $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
      echo $layout->render($data);
      ?>
      <!-- The file upload form used as target for the file upload widget -->
      <form id="fileupload" action="<?php echo JRoute::_('index.php?option=com_rental&task=images.upload&' . JSession::getFormToken() . '=1') ?>" method="POST" enctype="multipart/form-data">
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

            </div>
            <!-- The global progress information -->
            <div class="span5 fileupload-progress fade">
              <!-- The global progress bar -->
              <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                <div class="bar" style="width:0%;"></div>
              </div>
            </div>

            <div class="row-fluid">
              <div id="dropZone" class="well span9" style="min-height:125px;border:dashed 3px">
                <!-- The loading indicator is shown during file processing -->
                <div class="fileupload-loading"></div>
                <!-- The table listing the files available for upload/download -->
                <table role="presentation" class="table">
                  <tbody class="files"></tbody>
                </table>
              </div>
              <div class="span3">
                <div class="alert alert-notice">
                  <?php echo Jtext::_('COM_RENTAL_HELLOWORLD_IMAGE_UPLOAD_HELP'); ?>

                </div>

              </div>
            </div>
        </fieldset>
        <input type="hidden" name="review" value="<?php echo $this->unit->review ?>" />
        <input type="hidden" name="id" value="<?php echo $this->unit->id ?>" />
        <input type="hidden" name="property_id" value="<?php echo $this->unit->property_id ?>" />
        <input type="hidden" name="unit_id" value="<?php echo $this->unit->unit_id ?>" />
      </form>



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
            <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
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
        <img src="{%=file.thumbnailUrl%}">
        {% } %}
        </span>
        </td>
        <td>
        <p class="name">
        {% if (file.thumbnail_url) { %}
       
        <img src="{%=file.thumbnail_url%}" />
        
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
        <span>Clear</span>
        </button>
        {% } %}
        </td>
        </tr>
        {% } %}
      </script>

      <form action="<?php echo JRoute::_('index.php?option=com_rental'); ?>" method="post" name="adminForm" id="adminForm" class="form">
        <fieldset>
          <legend>
            <?php echo JText::sprintf('COM_RENTAL_IMAGES_EXISTING_IMAGE_LIST', $this->unit->unit_title); ?>
          </legend>
          <table id="articleList" class="table table-striped">
            <thead>
              <tr>
                <th class="hide">

                </th>
                <th>
            <div>
              <?php echo JText::_('COM_RENTAL_HELLOWORLD_IMAGE_ORDERING'); ?>
            </div>
            </th>
            <th class="center">
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
          <input type="hidden" name="unit_id" value="<?php echo $this->unit->unit_id ?>" />
          <input type="hidden" name="id" value="<?php echo $this->unit->id ?>" />
          <input type="hidden" name="property_id" value="<?php echo $this->unit->property_id ?>" />
          <input type="hidden" name="next" value="<?php echo base64_encode(JRoute::_('index.php?option=com_rental&task=availability.manage&unit_id=' . (int) $this->unit->unit_id . '&' . JSession::getFormToken() . '=1', false)); ?>" />

          <?php echo JHtml::_('form.token'); ?>

        </fieldset>
      </form>
    </div>
  </div>

