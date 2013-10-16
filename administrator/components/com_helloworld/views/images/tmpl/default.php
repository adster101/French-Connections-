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
  $saveOrderingUrl = 'index.php?option=com_helloworld&task=images.saveOrderAjax&tmpl=component';
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
      <div id="j-main-container" class="span10">
      <?php endif; ?>
      <?php
      $progress = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
      echo $progress->render($data);

      $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
      echo $layout->render($data);
      ?>
      <!-- The file upload form used as target for the file upload widget -->
      <form class="form-validate" id="fileupload" action="<?php echo JRoute::_('index.php?option=com_helloworld&task=images.upload&' . JSession::getFormToken() . '=1') ?>" method="GET" enctype="multipart/form-data">
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <noscript><input type="hidden" name="redirect" value="/"></noscript>
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <fieldset>
          <legend>
            <?php echo JText::_('COM_HELLOWORLD_IMAGES_UPLOAD_IMAGES'); ?>

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
                  <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
                </table>
              </div>
              <div class="span3">
                <div class="alert alert-notice">
                  <?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_IMAGE_UPLOAD_HELP'); ?>
                </div>
                <a href="#" class="btn" data-toggle="popover" data-placement="top" 
                   data-content="<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_IMAGE_UPLOAD_MORE_HELP') ?>" 
                   title="<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_MORE_IMAGES_HELP') ?>">
                  <span class="icon-help"></span>&nbsp;<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_MORE_HELP') ?>
                </a>     
              </div>
            </div>
        </fieldset>
        <input type="hidden" name="review" value="<?php echo $this->unit->review ?>" />
        <input type="hidden" name="id" value="<?php echo $this->unit->id ?>" />
        <input type="hidden" name="property_id" value="<?php echo $this->unit->property_id ?>" />
        <input type="hidden" name="unit_id" value="<?php echo $this->unit->unit_id ?>" />
      </form>




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

      <form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&unit_id=' . (int) $unit_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
        <fieldset>
          <legend>
            <?php echo JText::_('COM_HELLOWORLD_IMAGES_EXISTING_IMAGE_LIST'); ?>
          </legend>
          <table id="articleList" class="table table-striped">
            <thead>
              <tr>
                <th class="hide">

                </th>
                <th>
            <div class="hidden-desktop">
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_IMAGE_ORDERING'); ?>
            </div>
            </th>
            <th>
              <?php echo JText::_('COM_HELLOWORLD_IMAGES_CHOOSE_THUMBNAIL'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_IMAGES_THUMBNAIL'); ?>
            </th>
            <th>
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_IMAGES_CAPTION'); ?>
            </th>

            <th>
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_DELETE_IMAGE'); ?>
            </th>
            </tr>
            </thead>
            <tbody class="ui-sortable">    
              <?php echo $this->loadTemplate('image_list'); ?>
            </tbody>
            <input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />
          </table>
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="boxchecked" value="0" />
          <input type="hidden" name="unit_id" value="<?php echo $this->unit->unit_id ?>" />
          <input type="hidden" name="id" value="<?php echo $this->unit->id ?>" />

          <?php echo JHtml::_('form.token'); ?>

        </fieldset>
      </form>
    </div>

    <div class="span2">

    </div>
  </div>

