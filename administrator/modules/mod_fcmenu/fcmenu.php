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
			echo "<ul " . $id . " " . $class . ">\n";

			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current = & $child;
				$this->renderLevel($depth++);
			}

			echo "</ul>\n";
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
		$class = '';

		if ($this->_current->hasChildren())
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

		if ($this->_current->class == 'disabled')
		{
			$class = ' class="disabled"';
		}

		// Print the item
		echo "<li" . $class . ">";

		// Print a link if it exists
		$linkClass = array();
		$dataToggle = '';
		$dropdownCaret = '';

		if ($this->_current->hasChildren())
		{
			//$linkClass[] = 'dropdown-toggle';
      $linkClass[] = '';
			//$dataToggle = ' data-toggle="dropdown"';
      $dataToggle = '';

			if (!$this->_current->getParent()->hasParent())
			{
				//$dropdownCaret = ' <span class="caret"></span>';
			}
		}

		if ($this->_current->link != null && $this->_current->getParent()->title != 'ROOT')
		{
			$iconClass = $this->getIconClass($this->_current->class);

			if (!empty($iconClass))
			{
				$linkClass[] = $iconClass;
			}
		}

		// Implode out $linkClass for rendering
		$linkClass = ' class="' . implode(' ', $linkClass) . '"';

		if ($this->_current->link != null && $this->_current->target != null)
		{
			echo "<a" . $linkClass . " " . $dataToggle . " href=\"" . $this->_current->link . "\" target=\"" . $this->_current->target . "\" >"
				. $this->_current->title . $dropdownCaret . "</a>";
		}
		elseif ($this->_current->link != null && $this->_current->target == null)
		{
			echo "<a" . $linkClass . " " . $dataToggle . " href=\"" . $this->_current->link . "\">" . $this->_current->title . $dropdownCaret . "</a>";
		}
		elseif ($this->_current->title != null)
		{
			echo "<a" . $linkClass . " " . $dataToggle . ">" . $this->_current->title . $dropdownCaret . "</a>";
		}
		else
		{
			echo "<span></span>";
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
				echo '<ul class="">' . "\n";
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


}
