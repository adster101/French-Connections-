<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Tree based class to render the admin menu
 *
 * @package     Joomla.Administrator
 * @subpackage  mod_menu
 * @since       1.5
 */
class FcAdminCssMenu extends JAdminCssMenu
{

  /**
   * Method to render the menu
   *
   * @param   string  $id     The id of the menu to be rendered
   * @param   string  $class  The class of the menu to be rendered
   *
   * @return  void
   */
  public function renderMenu($id = 'menu', $class = '')
  {
    $depth = 1;

    if (!empty($id))
    {
      $id = 'id="' . $id . '"';
    }

    if (!empty($class))
    {
      $class = 'class="' . $class . '"';
    }

    // Recurse through children if they exist
    while ($this->_current->hasChildren())
    {
      echo "<div class='well'>";
      echo "<nav id='myNavmenu' class='navmenu navmenu-default navmenu-fixed-left offcanvas' role='navigation'>";
      echo "<ul " . $id . " " . $class . ">\n";

      foreach ($this->_current->getChildren() as $child)
      {
        $this->_current = & $child;
        $this->renderLevel($depth++);
      }

      echo "</ul>\n";
      echo "</nav>";
      echo "</div>";
    }

    if ($this->_css)
    {
      // Add style to document head
      JFactory::getDocument()->addStyleDeclaration($this->_css);
    }
  }

  /**
   * Method to render a given level of a menu
   *
   * @param   integer  $depth  The level of the menu to be rendered
   *
   * @return  void
   */
  public function renderLevel($depth)
  {
    // Build the CSS class suffix
    $itemClass = array();

    // Is this the current active linkage?
    if ($this->_current->active)
    {
      $itemClass[] = 'selected';
    }

    /* if ($this->_current->hasChildren())
      {
      $class = ' class="dropdown"';
      $class = '';
      }

      if ($this->_current->class == 'separator')
      {
      $class = ' class="divider"';
      }

      if ($this->_current->hasChildren() && $this->_current->class)
      {
      $class = ' class="dropdown-submenu"';
      $class = '';
      }
     */

    // Get the class attributes to add to the li item
    $itemClass[] = ($this->_current->hasChildren()) ? 'group' : 'item';
    $itemClass = ' class="' . implode(' ', $itemClass) . '"';

    // Print the item
    echo "<li" . $itemClass . ">";

    // Print a link if it exists
    $linkClass = array();
    $dataToggle = '';
    $dropdownCaret = '';
    $icon = '';



    if ($this->_current->hasChildren())
    {
      //$linkClass[] = 'dropdown-toggle';
      $linkClass[] = '';
      //$dataToggle = ' data-toggle="dropdown"';
      $dataToggle = '';
    }

    if ($this->_current->link != null && $this->_current->getParent()->title != 'ROOT')
    {
      $iconClass = $this->getIconClass($this->_current->class);

      if (!empty($iconClass))
      {
        $linkClass[] = $iconClass;
      }
    }

    if ($this->_current->class)
    {
      $icon = '<span class="icon icon-' . $this->_current->class . '">&nbsp;</span>&nbsp;';
    }


    // Implode out $linkClass for rendering
    $linkClass = ' class="item-label ' . implode(' ', $linkClass) . '"';

    // Check to see if this node has children, allows for nesting
    if ($this->_current->hasChildren())
    {
      if (!$this->_current->getParent()->hasParent()) // Check if this is a parent NODE (i.e. parent node is ROOT)
      {
        echo "<div class='nav-label top-level expanded'>";
      }
      // TO DO - Need to do something here to determine if this is the 'parent' of the current 
      // 'active' node. Something like $this->hasActiveChild() or similar.
    }

    // Spit out a placeholder node
    if ($this->_current->link == '#' && $this->_current->target == null)
    {
      echo $icon . $this->_current->title;
    }
    elseif ($this->_current->link != null && $this->_current->target != null)
    {
      echo "<a" . $linkClass . " " . $dataToggle . " href=\"" . $this->_current->link . "\" target=\"" . $this->_current->target . "\" >"
      . $icon . $this->_current->title . "</a>";
    }
    elseif ($this->_current->link != null && $this->_current->target == null)
    {
      echo "<a" . $linkClass . " " . $dataToggle . " href=\"" . $this->_current->link . "\">" . $icon . $this->_current->title . "</a>";
    }
    elseif ($this->_current->title != null)
    {
      echo "<a" . $linkClass . " " . $dataToggle . ">" . $icon . $this->_current->title . "</a>";
    }


    // Sames checks as above, but for closing tags
    if ($this->_current->hasChildren())
    {
      if (!$this->_current->getParent()->hasParent()) // Check if this is a parent NODE (i.e. parent node is ROOT)
      {
        echo "</div>";
      }
    }

    // Recurse through children if they exist
    while ($this->_current->hasChildren())
    {
      if ($this->_current->class)
      {
        $id = '';

        if (!empty($this->_current->id))
        {
          $id = ' id="menu-' . strtolower($this->_current->id) . '"';
        }

        echo '<ul' . $id . ' class="menu-component">' . "\n";
      }
      else
      {
        echo '<ul class="nav nav-list">' . "\n";
      }

      foreach ($this->_current->getChildren() as $child)
      {
        $this->_current = & $child;
        $this->renderLevel($depth++);
      }

      echo "</ul>\n";
    }

    echo "</li>\n";
  }

  /**
   * Method to determine whether the current menu item is current
   * Determined by scanning the URL
   * 
   */
  public function isActive($url = '')
  {
    $uri = JUri::getInstance();
    $current_url = $uri->toString();

    if (empty($url))
    {
      return false;
    }

    $active = strpos($current_url, $url);

    return $active;
  }

}
