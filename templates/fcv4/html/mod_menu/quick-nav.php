<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;


$user = JFactory::getUser();
$app = JFactory::getApplication();

// Note. It is important to remove spaces between elements.
?>
<?php // The menu class is deprecated. Use nav instead.  ?>
<ul class="nav <?php echo $class_sfx; ?>"<?php
$tag = '';

if ($params->get('tag_id') != null)
{
  $tag = $params->get('tag_id') . '';
  echo ' id="' . $tag . '"';
}
?>>
    <?php
      foreach ($list as $i => &$item)
      {
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
          $class .= ' divider';
        }

        if ($item->deeper)
        {
          $class .= ' deeper';
        }

        if ($item->parent)
        {
          $class .= ' parent';
        }
        
        if (!$user->guest && $item->deeper) { 
          $class = ' hide';
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
        if ($item->deeper && !$user->guest)
        {
          $anchor_class = $item->anchor_css ? 'class="' . $item->anchor_css . '" ' : '';

          echo '<li class="dropdown">';
          echo '<a ' . $anchor_class . ' data-toggle="dropdown" href="#" id="' . $item->id . '">' . $user->name . '<span class="caret"></span></a>';
          echo '<ul class="dropdown-menu pull-right" id="menu1">';
        }
        elseif ($item->shallower)
        {
          // The next item is shallower.
          echo '</li>';
          if (!$user->guest) {
            echo '<li role="presentation" class="divider"></li>';
            echo '<li><a href="' . JRoute::_('index.php?option=com_users&task=user.logout&' . JSession::getFormToken() . '=1') . '">' . JText::_('JLOGOUT') . '</a></li>';
          }
          echo str_repeat('</ul></li>', $item->level_diff);
          
        }
        else
        {
          // The next item is on the same level.
          echo '</li>';
        }
      }
      ?></ul>
