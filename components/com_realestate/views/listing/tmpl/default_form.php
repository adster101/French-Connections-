<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Below will be useful to be able to use the same params for enquiries as well as reviews
// $cparams = JComponentHelper::getParams('com_media');

$app = JFactory::getApplication();

//$id = $this->state->get('property.id');

$doc = JDocument::getInstance();

// Include the JDocumentRendererMessage class file
require_once JPATH_ROOT . '/libraries/joomla/document/html/renderer/message.php';
$render = new JDocumentRendererMessage($doc);
?>

<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$app = JFactory::getApplication();


$id = $this->item->property_id ? $this->item->property_id : '';

$errors = $app->getUserState('com_accommodation.enquiry.messages');
?>

<?php if (count($errors > 0)) : ?>

  <div class="contact-error">
    <?php echo $render->render($errors); ?>
  </div>

<?php endif; ?>

<div class="well well-sm well-light-blue">
  <form class="form-validate form-horizontal" id="contact-form" action="<?php echo JRoute::_('index.php?option=com_realestate&id=' . (int) $id) . '#email'; ?>" method="post">
    <?php echo JHtml::_('form.token'); ?>

    <fieldset class="adminform">
      <?php foreach ($this->form->getFieldset('enquiry') as $field): ?>
        <div class="form-group">
          <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
          <?php echo $field->label; ?>
            <?php echo $field->input; ?>
          </div>
        </div>         
      <?php endforeach; ?> 
    </fieldset>
    
    <div class="form-actions"><button class="btn btn-primary btn-large validate" type="submit"><?php echo JText::_('COM_ACCOMMODATION_SEND_ENQUIRY'); ?></button>
      <input type="hidden" name="option" value="com_realestate" />
      <input type="hidden" name="task" value="listing.enquiry" />
    </div>
  </form>

</div>


