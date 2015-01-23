<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');

// Register the global SearchHelper class
JLoader::register('SearchHelper', JPATH_LIBRARIES . '/frenchconnections/helpers/search.php');
$Itemid = SearchHelper::getItemid(array('component','com_registerowner'));
$non_ssl_url = JUri::getInstance();
$non_ssl_url->setScheme('http');

?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="form-login" class="form-vertical">
  <fieldset class="">
    <div class="form-group">
      <label for="mod-login-username" class="required">
        <?php echo JText::_('JGLOBAL_USERNAME'); ?>
      </label>
      <input name="username" tabindex="1" id="mod-login-username" type="text" class="form-control required" placeholder="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" size="15"/>
    </div>
    <div class="form-group">
      <span class="add-on">
        <label for="mod-login-password">
          <?php echo JText::_('JGLOBAL_PASSWORD'); ?>
        </label>
      </span>
      <input name="passwd" tabindex="2" id="mod-login-password" type="password" class="form-control required" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" size="15"/>
    </div>
    <?php if (count($twofactormethods) > 1): ?>
      <div class="form-group">
        <div class="controls">
          <label for="mod-login-secretkey">
            <?php echo JText::_('JGLOBAL_SECRETKEY'); ?>
          </label>
          <div class="input-group">
            <input name="secretkey" autocomplete="off" tabindex="3" id="mod-login-secretkey" type="text" class="form-control" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" size="15"/>
            <span class="input-group-addon" title="<?php echo JText::_('JGLOBAL_SECRETKEY_HELP'); ?>">
              <i class="glyphicon glyphicon-info-sign"></i>
            </span>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <div class="form-group">
      <div class="">
        <button tabindex="3" class="btn btn-primary btn-large">
          <i class="icon-lock icon-white"></i> <?php echo JText::_('MOD_LOGIN_LOGIN'); ?>
        </button>
      </div>
    </div>
    <p>   
      <a href="<?php echo JRoute::_($non_ssl_url->toString(array('scheme','host')) . '/advertise/register?view=resetpassword'); ?>">
        <strong>
          <span class='glyphicon glyphicon-info-sign'></span>
          &nbsp;Forgot you password?
        </strong>
      </a>
    </p>
    <p>
      <a href="<?php echo JRoute::_($non_ssl_url->toString(array('scheme','host')) . '/advertise/register'); ?>">
        <strong>
          <span class='glyphicon glyphicon-hand-right'></span>
          &nbsp;Don't have an account? Sign up.
        </strong>
      </a>
    </p>

    <input type="hidden" name="option" value="com_login"/>
    <input type="hidden" name="task" value="login"/>
    <input type="hidden" name="return" value="<?php echo $return; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
  </fieldset>
</form>

