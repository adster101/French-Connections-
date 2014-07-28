<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('behavior.noframes');

// Get the login modules
// If you want to use a completely different login module change the value of name
// in your layout override.
?>

<div class="">

  <div class="row-fluid">
    <div class="span6">
      <?php
      $loginmodule = LoginModelLogin::getLoginModule('mod_login');
      echo JModuleHelper::renderModule($loginmodule, array('style' => 'well', 'id' => 'section-box'));
      ?>
    </div>
    <div class="span6">

      <?php
      //Get any other modules in the login position.
      //If you want to use a different position for the modules, change the name here in your override.
      $modules = JModuleHelper::getModules('login');

      foreach ($modules as $module)
      // Render the login modules
        if ($module->module != 'mod_login') {
          echo JModuleHelper::renderModule($module, array('style' => 'html5', 'id' => 'section-box'));
        }
      ?>
    </div>
  </div>
</div>