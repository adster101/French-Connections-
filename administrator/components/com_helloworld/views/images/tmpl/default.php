<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

$saveOrder = true;
if ($saveOrder) {
  $saveOrderingUrl = 'index.php?option=com_content&task=articles.saveOrderAjax&tmpl=component';
  JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$app = JFactory::getApplication();

$input = $app->input;

$id = $input->get('id','','int');

$data = JApplication::getUserState('listing', '');
?>
<script type="text/javascript">
  Joomla.orderTable = function()
  {
    table = document.getElementById("sortTable");
    direction = document.getElementById("directionTable");
    order = table.options[table.selectedIndex].value;
    if (order != '<?php echo $listOrder; ?>')
    {
      dirn = 'asc';
    }
    else
    {
      dirn = direction.options[direction.selectedIndex].value;
    }
    Joomla.tableOrdering(order, dirn, '');
  }
</script>

<?php if (!empty($this->sidebar)): ?>
  <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
  </div>
  <div id="j-main-container" class="span10">
  <?php else : ?>
    <div id="j-main-container">
    <?php endif; ?>
    <?php
    $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
    echo $layout->render($data);
    ?>  
    <div id="collapseUpload" class="in collapse">
      <hr/>
      <!-- The file upload form used as target for the file upload widget -->
      <form class="form-validate" id="fileupload" action="<?php echo JRoute::_('index.php?option=com_helloworld&task=images.upload&' . JSession::getFormToken() . '=1&id=' . (int) $id); ?>" method="GET" enctype="multipart/form-data">
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <noscript><input type="hidden" name="redirect" value="/"></noscript>
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="">
          <fieldset>
            <legend>
              <?php echo JText::_('COM_HELLOWORLD_IMAGES_UPLOAD_IMAGES'); ?>
            </legend>
            <div class="row-fluid fileupload-buttonbar">
              <div class="span7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                  <i class="icon-plus icon-white"></i>
                  <span>Add files...</span>
                  <input type="file" name="jform[files]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                  <i class="icon-upload icon-white"></i>
                  <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                  <i class="icon-trash icon-white"></i>
                  <span>Cancel upload</span>
                </button>
                <button type="button" class="btn btn-danger delete">
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
            <div class="row-fluid">
              <div class="span9">
                <!-- The loading indicator is shown during file processing -->
                <div class="fileupload-loading"></div>
                <!-- The table listing the files available for upload/download -->
                <table role="presentation" class="table table-striped">
                  <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>

                </table>    
              </div>
              <div class="span3">
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
            <hr />
          </fieldset>
      </form>

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
      <form action="<?php echo JRoute::_('index.php?option=com_helloworld'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
        <fieldset>
          <legend>
            <?php echo JText::_('COM_HELLOWORLD_IMAGES_EXISTING_IMAGE_LIST'); ?>
          </legend>
          <table id="articleList" class="table table-striped">
            <thead>
              <tr>
                <th width="3%" class="nowrap  hidden-phone">
                  <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                </th>
                <th width="3%">
                  <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
                </th>			
                <th width="10%">
                  <?php echo JText::_('COM_HELLOWORLD_THUMBNAIL'); ?>              
                </th>
                <th width="25%">
                  <?php echo JText::_('COM_HELLOWORLD_OFFERS_HEADING_GREETING'); ?>
                </th>
                <th>
                  <?php echo JText::_('COM_HELLOWORLD_IMAGES_CHOOSE_THUMBNAIL'); ?>
                </th>
              </tr>
            </thead>
            <?php
            $listOrder = $this->escape($this->state->get('list.ordering'));
            $user = JFactory::getUser();
            $userId = $user->id;
            $groups = $user->getAuthorisedGroups();
            $ordering = ($listOrder == 'a.lft');
            $originalOrders = array();

            foreach ($this->items as $i => $item):
              ?>
              <tr>
                <td>
                  <span class="sortable-handler hasTooltip <?php //echo $disableClassName;     ?>" title="<?php //echo $disabledLabel;     ?>">
                    <i class="icon-move"></i>
                  </span>
                  <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                </td>
                <td class="hidden-phone">
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td> 
                  <img width="75" src="<?php echo '/images/property/' . (int) $this->items[0]->property_id . '/thumbs/' . $item->image_file_name; ?>" />
                </td>
                <td>         
                  <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=image.edit&layout=update&id=' . (int) $item->id) ?>">
                    <?php echo $this->escape($item->caption); ?>
                  </a>
                </td>
                <td>
                  <input type="radio" name="image_id[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                </td>
              </tr>				

            <?php endforeach; ?>

            <tr>
              <td colspan="7">
              </td>
            </tr>



            <input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />
            <input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />


          </table>
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="boxchecked" value="0" />
          <?php echo JHtml::_('form.token'); ?>
          </div>
        </fieldset>
      </form>


