<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.formvalidation');
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = true;

if ($saveOrder)
{
  $saveOrderingUrl = 'index.php?option=com_rental&task=images.saveOrderAjax&tmpl=component';
  //JHtml::_('fcsortablelist.fcsortable', 'imageList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false);
}


$data = array('status' => $this->status);
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
      $tabs = new JLayoutFile('realestate_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_realestate/layouts');
      echo $tabs->render($data);

      $layout = new JLayoutFile('frenchconnections.property.realestate_tabs');
      echo $layout->render($data);
      ?>
      <?php echo $this->loadTemplate('upload'); ?>

      <form action="<?php echo JRoute::_('index.php?option=com_realestate'); ?>" method="post" name="adminForm" id="adminForm" class="form">
        <fieldset>
          <legend>
            <?php echo JText::sprintf('COM_RENTAL_IMAGES_EXISTING_IMAGE_LIST', $this->property->title); ?>
          </legend>
          <div class="image-gallery">
            <?php if (!empty($this->items)) : ?>
              <?php echo $this->loadTemplate('image_list'); ?>
            <?php else: ?>
              <div class="alert alert-info">
                <?php echo JText::_('COM_RENTAL_RENTAL_IMAGE_GALLERY_EMPTY'); ?>
              </div> 
            <?php endif; ?>    
          </div>
          <input type="hidden" name="extension" value="<?php echo 'com_realestate'; ?>" />
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="boxchecked" value="0" />
          <input type="hidden" name="id" value="<?php echo (int) $this->property->id ?>" />
          <input type="hidden" name="property_id" value="<?php echo (int) $this->property->realestate_property_id ?>" />
          <?php echo JHtml::_('form.token'); ?>
        </fieldset>
      </form>
    </div>
  </div>


