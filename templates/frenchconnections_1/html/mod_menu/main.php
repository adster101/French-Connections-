<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$menu_type = $params->get('menutype', '');

// Note. It is important to remove spaces between elements.
?>

  <ul class="nav <?php echo $class_sfx; ?>"<?php
      $tag = '';
      if ($params->get('tag_id') != null)
      {
        $tag = $params->get('tag_id') . '';
        echo ' id="' . $tag . '"';
      }
      ?>>
        <?php
        foreach ($list as $i => &$item) :
          $class = 'item-' . $item->id;
          if ($item->id == $active_id)
          {
            $class .= ' current';
          }

          if (in_array($item->id, $path))
          {
            $class .= ' active';
          }
          elseif ($item->type == 'alias')
          {
            $aliasToId = $item->params->get('aliasoptions');
            if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
            {
              $class .= ' active';
            }
            elseif (in_array($aliasToId, $path))
            {
              $class .= ' alias-parent-active';
            }
          }

          if ($item->type == 'separator')
          {
            $class .= ' divider-vertical';
          }

          if ($item->deeper)
          {
            $class .= ' deeper';
          }

          if ($item->parent)
          {
            $class .= ' parent';
          }

          if (!empty($class))
          {
            $class = ' class="' . trim($class) . '"';
          }

          echo '<li' . $class . '>';

          // Render the menu item.
          switch ($item->type) :
            case 'separator':
            case 'url':
            case 'component':
            case 'heading':
              require JModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
              break;

            default:
              require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
              break;
          endswitch;

          // The next item is deeper.
          if ($item->deeper)
          {

            echo '<ul class="nav-child unstyled small">';
          }
          // The next item is shallower.
          elseif ($item->shallower)
          {

            echo '</li>';

            echo str_repeat('</ul></li>', $item->level_diff);
            // If next item is shallower and this item is level 2 (next one must be level 1 so show a divider)
            if (($item->level == 2))
            {
              echo '<li class="divider-vertical"></li>';
            }
          }
          // The next item is on the same level.
          else
          {
            echo '</li>';
            // Additional divider if we are item level 1 (e.g. initial menu item)
            if ($item->level == 1)
            {
              echo '<li class="divider-vertical"></li>';
            }
          }



        endforeach;
        ?>
  </ul>