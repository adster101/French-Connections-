<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$user = JFactory::getUser();
$app = JFactory::getApplication();

if (!$user->guest) {
  //echo 'You are logged in as:<br />';
  //echo 'User name: ' . $user->username . '<br />';
  //echo 'Real name: ' . $user->name . '<br />';
  //echo 'User ID  : ' . $user->id . '<br />';
}

//JHtml::_('bootstrap.dropdown');
// Note. It is important to remove spaces between elements.
?>
<div class='pull-right'>
  <?php if ($user->guest) : // User is guest. Just want to show a link to the login screen     ?> 
    <ul role="menu" aria-labelledby="dLabel" class="nav <?php echo $class_sfx; ?>">
      <?php
      foreach ($list as $i => &$item) :
        $class = 'item-' . $item->id;
        if ($item->id == $active_id) {
          $class .= ' current';
        }

        if (in_array($item->id, $path)) {
          $class .= ' active';
        }
        if (!empty($class)) {
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
        echo '</li>';
        ?>
      <?php endforeach;
      ?>
    </ul>
  <?php else : ?> 
    <div class="dropdown my-french">

      <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="#">
        <?php echo htmlspecialchars($user->name); ?>
        <b class="caret"></b>
      </a>

      <ul role="menu" aria-labelledby="dLabel" class="dropdown-menu pull-right" <?php
          $tag = '';
          if ($params->get('tag_id') != null) {
            $tag = $params->get('tag_id') . '';
            echo ' id="' . $tag . '"';
          }
          ?>>
            <?php
            foreach ($list as $i => &$item) :
              if ($i == 0) {
                continue;
              }
              $class = 'item-' . $item->id;
              if ($item->id == $active_id) {
                $class .= ' current';
              }

              if (in_array($item->id, $path)) {
                $class .= ' active';
              } elseif ($item->type == 'alias') {
                $aliasToId = $item->params->get('aliasoptions');
                if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
                  $class .= ' active';
                } elseif (in_array($aliasToId, $path)) {
                  $class .= ' alias-parent-active';
                }
              }

              if ($item->type == 'separator') {
                $class .= ' divider-vertical';
              }

              if ($item->deeper) {
                $class .= ' deeper';
              }

              if ($item->parent) {
                $class .= ' parent';
              }

              if (!empty($class)) {
                $class = ' class="' . trim($class) . '"';
              }

              echo '<li role="presentation" ' . $class . '>';

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
              if ($item->deeper) {

                echo '<ul class="nav-child unstyled small">';
              }
              // The next item is shallower.
              elseif ($item->shallower) {

                echo '</li>';

                //echo str_repeat('</ul></li>', $item->level_diff);
                // If next item is shallower and this item is level 2 (next one must be level 1 so show a divider)
              }  
              // The next item is on the same level.
              else {
                echo '</li>';
                // Additional divider if we are item level 1 (e.g. initial menu item)
                
                
              }
            if (($i +1) == count($list)) {
                ?>
 <li role="presentation" class="divider"></li>       
            <li>
              <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&' . JSession::getFormToken() . '=1') ?>"><?php echo JText::_('JLOGOUT'); ?></a>
            </li>
              <?php } 
              
          

        endforeach;

        // Add a logout link
        ?>

      </ul>
    </div>

  <?php endif; ?>

</div>