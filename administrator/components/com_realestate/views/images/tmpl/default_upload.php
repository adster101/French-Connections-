<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


JHtml::_('bootstrap.popover');
?>

<!--The file upload form used as target for the file upload widget-->
<form id="fileupload" action="<?php echo JRoute::_('index.php?option=com_realestate&task=images.upload&' . JSession::getFormToken() . '=1') ?>" method="POST" enctype="multipart/form-data">
  <!-- Redirect browsers with JavaScript disabled to the origin page -->
  <!-- Redirect browsers with JavaScript disabled to the origin page -->
  <noscript><input type="hidden" name="redirect" value="/"></noscript>
  <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
  <fieldset>
    <legend>
      <?php echo JText::_('COM_RENTAL_IMAGES_UPLOAD_IMAGES'); ?>
    </legend>
    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    <div class="row-fluid fileupload-buttonbar">
      <div class="span5">
        <!-- The fileinput-button span is used to style the file input field as button -->
        <span class="btn btn-success fileinput-button">
          <i class="glyphicon glyphicon-plus"></i>
          <span><?php echo JText::_('COM_RENTAL_UPLOAD_IMAGES_BROWSE'); ?></span>
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
        &nbsp;&nbsp;
        <a class="hasPopover" 
           data-placement="bottom" 
           title="<?php echo JText::_('COM_RENTAL_UPLOAD_IMAGES_HELP_TITLE') ?>" 
           data-content="<?php echo JText::_('COM_RENTAL_UPLOAD_IMAGES_HELP') ?>">
          <i class="icon icon-info"> </i> Help
        </a>
      </div>
      <div class="span7">
        <!-- The global file processing state -->
        <span class="fileupload-process"></span>
        <!-- The global progress state -->
        <div class="fileupload-progress fade">
          <!-- The global progress bar -->
          <div class="progress progress-success active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <div class="bar progress-bar-success" style="width:0%;"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- The table listing the files available for upload/download -->
    <div class="well well-small">
      <div class="fileupload-loading"></div>
      <ul role="presentation" class="files clearfix"></ul>
    </div>
    <input type="hidden" name="review" value="<?php echo $this->property->review ?>" />
    <input type="hidden" name="id" value="<?php echo $this->property->id ?>" />
    <input type="hidden" name="property_id" value="<?php echo $this->property->realestate_property_id ?>" />
  </fieldset>
</form>