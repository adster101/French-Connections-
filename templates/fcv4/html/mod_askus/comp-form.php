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
?>
<div id="askus">

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
        <?php
        if ($module->showtitle) {
            echo '<' . $headerTag . ' class=' . htmlspecialchars($headerClass) . '>' . $module->title . '</' . $headerTag . '>';
        }
        ?>
        <div class='row'>
            <div class='col-lg-6 col-lg-offset-3'>
                <form id="contact-form" action="<?php echo JRoute::_('/contact-us?competition=true&name=win2000'); ?>" method="post" class="">
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
                          <label class="muted small">
                            Tick this box if you would prefer not to receive special offers and news from French Connections. We take your privacy very seriously and all subscribers can unsubscribe at any time.
                            <input type="checkbox" name="optout" />
                          </label>
                          <label>
                            I've read and agree to the <a href="/win-2000-in-cash/competition-terms-conditions" target="_blank">Terms and Conditions</a> for the prize draw
                            <input type="checkbox" name="termsandconditions" required />
                          </label>
                        </div>

                        <button class="btn btn-primary btn-lg btn-block " type="submit">
                            <?php echo JText::_('Enter'); ?>
                        </button>
                        <input type="hidden" name="task" value="contact.send" />
                        <input type="hidden" name="return" value="<?php echo base64_encode(JUri::getInstance()->toString() . '/thank-you'); ?>" />

                        <?php echo JHtml::_('form.token'); ?>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
