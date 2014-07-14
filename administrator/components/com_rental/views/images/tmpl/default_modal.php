<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="collapseModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Upload pics baby</h3>
  </div>
  <div class="modal-body">
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
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-primary" onclick="<?php echo $cmd; ?>">
      <?php echo JText::_('JSUBMIT'); ?>
    </button>
  </div>
</div>