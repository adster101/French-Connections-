<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();

  $menu = $app->getMenu();
    $menuItem = $menu->getActive();

    if ($this->user->get('guest')):
	// The user is not logged in.
  if ($menuItem->id == 119) {
    echo "Naughty, naughty, very naughty!!"; 
  } else {
  	echo $this->loadTemplate('login');
  }
else:
	// The user is already logged in.
	echo $this->loadTemplate('logout');
endif;
