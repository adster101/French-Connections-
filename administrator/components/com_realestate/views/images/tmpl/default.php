<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
//JHtml::_('bootstrap.tooltip');
//JHtml::_('behavior.formvalidator');
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = true;

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_realestate&task=images.saveOrderAjax&tmpl=component';
    JHtml::_('fcsortablelist.fcsortable', 'imageList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false);
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

                    <input type="hidden" name="next" value="<?php echo base64_encode(JRoute::_('index.php?option=com_realestate&task=payment.summary&realestate_property_id=' . (int) $this->property->realestate_property_id, false, 1)); ?>" />

                    <?php echo JHtml::_('form.token'); ?>
                </fieldset>
            </form>
        </div>
    </div>

    <div id="modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">
                <?php echo JText::_('COM_RENTAL_HELLOWORLD_UPDATE_CAPTION'); ?>
            </h3>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </div>
    </div>

    <script>
        /**
         * Default function. Usually would be overriden by the component
         */
        Joomla.submitbutton = function (pressbutton) {
          if (pressbutton == 'images.updatecaption') {

            form = document.getElementById("captionForm");

            if (document.formvalidator.isValid(form))
            {
              Joomla.submitform('images.updatecaption', form);

            } else {
              alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED', ''));
              return false;
            }

          } else {
            Joomla.submitform(pressbutton);
          }
        }


    </script>

