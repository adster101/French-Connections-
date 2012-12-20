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



jimport('joomla.html.html.bootstrap');
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
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
		$app    = JFactory::getApplication();


$id = $this->item->id ? $this->item->id : '' ;
?>
	<div class="contact-error">

		<?php print_r ($app->getUserState('com_accommodation.enquiry.data')); ?>

	</div><div class="well">
	<form id="contact-form" action="<?php echo JRoute::_('index.php?option=com_accommodation&view=property&id='.$id); ?>" method="post" class="form-validate form-horizontal">
			<fieldset class="adminform">
      <?php foreach ($this->form->getFieldset('enquiry') as $field): ?>
        <div class="control-group">
          <?php echo $field->label; ?>
          <div class="controls">
            <?php echo $field->input; ?>
          </div>
        </div>         
      <?php endforeach; ?>
			<div class="form-actions"><button class="btn btn-primary validate" type="submit"><?php echo JText::_('COM_REVIEW_REVIEW_SUBMIT'); ?></button>
				<input type="hidden" name="option" value="com_accommodation" />
				<input type="hidden" name="task" value="enquiry.submit" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</fieldset>
	</form>
</div>