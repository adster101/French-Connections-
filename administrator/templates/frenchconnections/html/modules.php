<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.isis
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */
/*
 * Module chrome for rendering the module in a submenu
 */
function modChrome_title($module, &$params, &$attribs) {
  if ($module->content) {
    echo "<div class=\"module-title\"><h6>" . $module->title . "</h6></div>";
    echo $module->content;
  }
}

function modChrome_no($module, &$params, &$attribs) {
  if ($module->content) {
    echo $module->content;
  }
}

function modChrome_well($module, &$params, &$attribs) {
  if ($module->content) {
    $bootstrapSize = $params->get('bootstrap_size');
    $moduleClass = !empty($bootstrapSize) ? ' span' . (int) $bootstrapSize . '' : '';

    if ($moduleClass) {
      echo '<div class="' . $moduleClass . '">';
    }

    echo '<div class="well well-small">';
    echo '<h2 class="module-title nav-header">' . $module->title . '</h2>';
    echo $module->content;
    echo '</div>';

    if ($moduleClass) {
      echo '</div>';
    }
  }
}

/*
 * html5 (chosen html5 tag and font headder tags)
 */

function modChrome_panel($module, &$params, &$attribs) {

  $moduleTag = $params->get('module_tag', 'div');
  $headerTag = htmlspecialchars($params->get('header_tag', 'h3'));
  $bootstrapSize = (int) $params->get('bootstrap_size', 0);
  $moduleClass = $bootstrapSize != 0 ? ' span' . $bootstrapSize : '';

  // Temporarily store header class in variable
  $headerClass = $params->get('header_class');
  $headerClass = !empty($headerClass) ? ' class="' . htmlspecialchars($headerClass) . '"' : '';

  if (!empty($module->content)) :
    ?>
    <<?php echo $moduleTag; ?> class="moduletable<?php echo $moduleClass; ?>">

    <!-- This outputs another div but nests the module class suffix into the chrome -->
    <<?php echo $moduleTag; ?> class="<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">

    <?php if ((bool) $module->showtitle) : ?>
      <?php echo '<div class="panel-heading">' ?>
      <<?php echo $headerTag . $headerClass . '>' . $module->title; ?></<?php echo $headerTag; ?>>
      <?php echo '</div>'; ?>
    <?php endif; ?>

    <?php echo '<div class="panel-body">' ?>


    <?php echo $module->content; ?>

    </<?php echo $moduleTag; ?>>
    </<?php echo $moduleTag; ?>>
    </<?php echo $moduleTag; ?>>

  <?php
  endif;
}
