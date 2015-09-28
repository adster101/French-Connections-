<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$headerClass = $params->get('header_class');
$headerTag = htmlspecialchars($params->get('header_tag', 'h3'));

if ($module->showtitle) {
    echo '<' . $headerTag . ' class=' . htmlspecialchars($headerClass) . '>' . $module->title . '</' . $headerTag . '>';
}
?>
<p class='text-center'><?php echo JText::_('MOD_ASKUS_GOT_A_QUESTION_BLURB') ?>
<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JHtml::_('behavior.formvalidator');
JHtml::_('bootstrap.tooltip');
?>
<div class='advertise-askus'>
    <div class='row'>
        <div class='col-lg-6 col-lg-offset-3'>
            <form id="contact-form" action="<?php echo JRoute::_('/contact-us?askus=true'); ?>" method="post" class="">
                <fieldset class="adminform">
                    <div class="form-group">
<?php echo $form->getLabel('name'); ?>
                        <?php echo $form->getInput('name'); ?>
                    </div>
                    <div class="form-group">
<?php echo $form->getLabel('email'); ?> 
                        <?php echo $form->getInput('email'); ?>
                    </div>
                    <div class="form-group">
<?php echo $form->getLabel('tel'); ?> 
                        <?php echo $form->getInput('tel'); ?>
                    </div>
                    <div class="form-group">
<?php echo $form->getLabel('message'); ?> 
                        <?php echo $form->getInput('message'); ?>
                    </div>
                        <?php echo $form->getLabel('nature'); ?>
                    <?php echo $form->getInput('nature'); ?>

                    <button class="btn btn-primary btn-lg btn-block " type="submit">
<?php echo JText::_('COM_FCCONTACT_SEND_MESSAGE'); ?>
                    </button>
                    <input type="hidden" name="task" value="contact.send" />
                    <input type="hidden" name="return" value="<?php echo base64_encode(JUri::getInstance()->toString() . '?sent=true'); ?>" />

<?php echo JHtml::_('form.token'); ?>
                </fieldset>
            </form>
        </div>
    </div>
</div>