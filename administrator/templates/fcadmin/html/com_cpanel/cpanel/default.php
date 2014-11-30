<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cpanel
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$user = JFactory::getUser();
$groups = JAccess::getGroupsByUser($user->id, false);
?>

<div class="row-fluid">

  <?php if (in_array(10, $groups)) : ?>
    <?php $iconmodules = JModuleHelper::getModules('owner'); ?>
    <div class="span12">
      <div class="cpanel-links">
        <?php
        // Display the submenu position modules
        foreach ($iconmodules as $iconmodule) {
          echo JModuleHelper::renderModule($iconmodule, array('style' => 'html5'));
        }
        ?>
      </div>
    </div>
  <?php else : ?>
    <?php $iconmodules = JModuleHelper::getModules('icon'); ?>
    <?php if ($iconmodules) : ?>
      <div class="span3">
        <div class="cpanel-links">
          <?php
          // Display the submenu position modules
          foreach ($iconmodules as $iconmodule) {
            echo JModuleHelper::renderModule($iconmodule);
          }
          ?>
        </div>
      </div>
    <?php endif; ?>
    <div class="span<?php echo ($iconmodules) ? 9 : 12; ?>">

      <?php
      $spans = 0;
      foreach ($this->modules as $module) {
        // Get module parameters
        $params = new JRegistry;
        $params->loadString($module->params);
        echo JModuleHelper::renderModule($module, array('style' => 'well'));
      }
      ?>
    </div>
  </div>
<?php endif; ?>
</div>
