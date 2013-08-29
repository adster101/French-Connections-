<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/* @var $menu JAdminCSSMenu */

$shownew = (boolean) $params->get('shownew', 1);
$showhelp = $params->get('showhelp', 1);
$user = JFactory::getUser();
$lang = JFactory::getLanguage();

/**
 * Property Submenu
 */
$addRental = $user->authorise('core.create', 'com_helloworld');
$addRealestate = $user->authorise('core.create', 'com_helloworld');
$addOffer = $user->authorise('core.create', 'com_specialoffers');
$addReview = $user->authorise('core.create', 'com_reviews');

$manage_rental = $user->authorise('core.manage', 'com_helloworld');
$manage_realestate = $user->authorise('core.manage', 'com_helloworld');
$manage_offers = $user->authorise('core.manage', 'com_specialoffers');

$manage_account = $user->authorise('core.manage', 'com_invoices');

$manage_users = $user->authorise('core.manage', 'com_users');

$menu->addChild(new JMenuNode(JText::_('COM_ADMIN_HOME'), 'index.php'),true);
$menu->getParent();

if ($manage_rental || $manage_realestate) {

  $menu->addChild(new JMenuNode(JText::_('COM_ADMIN_RENTAL_PROPERTY'), '#'), true);

  if ($manage_rental) {

    $menu->addChild(new JMenuNode(JText::_('COM_HELLOWORLD_MENU'), 'index.php?option=com_helloworld', 'class:property'), true);
    $menu->addChild(new JMenuNode(JText::_('COM_HELLOWORLD_VIEW_ALL_MENU'), 'index.php?option=com_helloworld', 'class:property'));

    if ($addRental) {

      $menu->addChild(
              new JMenuNode(JText::_('COM_PROPERTY_CREATE_NEW_RENTAL_PROPERTY'), 'index.php?option=com_helloworld&task=propertyversions.add', 'class:newproperty')
      );
    }

    // Determine the parent node for whichever child element we are adding
    $menu->getParent();
  }

  if ($manage_offers) {
    $menu->addSeparator();

    $menu->addChild(new JMenuNode(JText::_('COM_SPECIALOFFERS_MENU'), 'index.php?option=com_specialoffers', 'class:specialoffers'), true);

    if ($addOffer) {
      $menu->addChild(
              new JMenuNode(JText::_('COM_SPECIALOFFERS_MENU_ADD_NEW'), 'index.php?option=com_specialoffers&task=specialoffer.add', 'class:specialoffers')
      );
    }

    // Get the parent of the above added child, if you can't create an offer this will be the special offers
    $menu->getParent();
  }

  $menu->addSeparator();

  $menu->addChild(
          new JMenuNode(JText::_('COM_ENQUIRIES_MENU'), 'index.php?option=com_enquiries', 'class:enquiries'), true
  );

  $menu->getParent();

  $menu->addSeparator();

  $menu->addChild(new JMenuNode(JText::_('COM_REVIEWS_MENU'), 'index.php?option=com_reviews', 'class:reviews'), true);

  $menu->getParent();

  $menu->addSeparator();

  $menu->addChild(new JMenuNode(JText::_('COM_STATS_MENU'), 'index.php?option=com_stats', 'class:stats'), true);

  $menu->getParent();



  // Determine the parent of the firstly added node
  $menu->getParent();
}

if ($manage_realestate) {
  $menu->addChild(new JMenuNode(JText::_('COM_ADMIN_REAL_ESTATE_PROPERTY'), '#'), true);


  if ($manage_realestate) {

    $menu->addChild(new JMenuNode(JText::_('Real Estate Property'), '#', 'class:realestateproperty'), true);

    if ($addRealestate) {
      $menu->addChild(
              new JMenuNode(JText::_('COM_PROPERTY_CREATE_NEW_REAL_ESTATE_PROPERTY'), '#', 'class:newrealestateproperty')
      );
    }
    $menu->getParent();
  }
  $menu->addSeparator();

  $menu->addChild(
          new JMenuNode(JText::_('Enquiries'), 'index.php?option=com_enquiries', 'class:enquiries'));

  $menu->addSeparator();

  $menu->addChild(new JMenuNode(JText::_('Statistics'), 'index.php?option=com_stats', 'class:stats'));

  $menu->getParent();
}

/**
 * Account menu
 */
if ($manage_account) {

  $menu->addChild(new JMenuNode(JText::_('COM_ACCOUNTS_MENU'), '#'), true);

  if (!$manage_users) {

    $menu->addChild(new JMenuNode(JText::_('Manage Account Details'), 'index.php?option=com_accounts&task=account.edit&user_id=' . (int) $user->id, 'class:accounts'));
    $menu->addSeparator();
  }



  $menu->addChild(new JMenuNode(JText::_('Invoice History'), 'index.php?option=com_invoices&view=invoices', 'class:accounts'), true);

  $menu->getParent();

  $menu->getParent();
}

/**
 * Site SubMenu
 * */
if (in_array(8, $user->getAuthorisedGroups())) {
  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_SYSTEM'), '#'), true
  );
  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_CONTROL_PANEL'), 'index.php', 'class:cpanel')
  );

  $menu->addSeparator();

  if ($user->authorise('core.admin')) {
    $menu->addChild(new JMenuNode(JText::_('MOD_MENU_CONFIGURATION'), 'index.php?option=com_config', 'class:config'));
    $menu->addSeparator();
  }

  $chm = $user->authorise('core.manage', 'com_checkin');
  $cam = $user->authorise('core.manage', 'com_cache');

  if ($chm || $cam) {
    // Keep this for when bootstrap supports submenus?
    /* $menu->addChild(
      new JMenuNode(JText::_('MOD_MENU_MAINTENANCE'), 'index.php?option=com_checkin', 'class:maintenance'), true
      ); */

    if ($chm) {
      $menu->addChild(new JMenuNode(JText::_('MOD_MENU_GLOBAL_CHECKIN'), 'index.php?option=com_checkin', 'class:checkin'));
      $menu->addSeparator();
    }

    if ($cam) {
      $menu->addChild(new JMenuNode(JText::_('MOD_MENU_CLEAR_CACHE'), 'index.php?option=com_cache', 'class:clear'));
      $menu->addChild(new JMenuNode(JText::_('MOD_MENU_PURGE_EXPIRED_CACHE'), 'index.php?option=com_cache&view=purge', 'class:purge'));
    }

    // $menu->getParent();
  }

  $menu->addSeparator();

  if ($user->authorise('core.admin')) {
    $menu->addChild(
            new JMenuNode(JText::_('MOD_MENU_SYSTEM_INFORMATION'), 'index.php?option=com_admin&view=sysinfo', 'class:info')
    );
  }

  $menu->getParent();
}

/**
 * Users Submenu
 * 
 */
if ($manage_users) {
  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_COM_USERS_USERS'), '#'), true
  );
  $createUser = $shownew && $user->authorise('core.create', 'com_users');
  $createGrp = $user->authorise('core.admin', 'com_users');

  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_COM_USERS_USER_MANAGER'), 'index.php?option=com_users&view=users', 'class:user'), $createUser
  );

  if ($createUser) {
    $menu->addChild(
            new JMenuNode(JText::_('MOD_MENU_COM_USERS_ADD_USER'), 'index.php?option=com_users&task=user.add', 'class:newarticle')
    );
    $menu->getParent();
  }

  if ($createGrp) {
    $menu->addChild(
            new JMenuNode(JText::_('MOD_MENU_COM_USERS_GROUPS'), 'index.php?option=com_users&view=groups', 'class:groups'), $createUser
    );

    if ($createUser) {
      $menu->addChild(
              new JMenuNode(JText::_('MOD_MENU_COM_USERS_ADD_GROUP'), 'index.php?option=com_users&task=group.add', 'class:newarticle')
      );
      $menu->getParent();
    }

    $menu->addChild(
            new JMenuNode(JText::_('MOD_MENU_COM_USERS_LEVELS'), 'index.php?option=com_users&view=levels', 'class:levels'), $createUser
    );

    if ($createUser) {
      $menu->addChild(
              new JMenuNode(JText::_('MOD_MENU_COM_USERS_ADD_LEVEL'), 'index.php?option=com_users&task=level.add', 'class:newarticle')
      );
      $menu->getParent();
    }
  }

  $menu->addSeparator();
  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_COM_USERS_NOTES'), 'index.php?option=com_users&view=notes', 'class:user-note'), $createUser
  );

  if ($createUser) {
    $menu->addChild(
            new JMenuNode(JText::_('MOD_MENU_COM_USERS_ADD_NOTE'), 'index.php?option=com_users&task=note.add', 'class:newarticle')
    );
    $menu->getParent();
  }

  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_COM_USERS_NOTE_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_users', 'class:category'), $createUser
  );

  if ($createUser) {
    $menu->addChild(
            new JMenuNode(JText::_('MOD_MENU_COM_CONTENT_NEW_CATEGORY'), 'index.php?option=com_categories&task=category.add&extension=com_users.notes', 'class:newarticle')
    );
    $menu->getParent();
  }

  $menu->addSeparator();
  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_MASS_MAIL_USERS'), 'index.php?option=com_users&view=mail', 'class:massmail')
  );

  $menu->getParent();
}

/**
 * Menus Submenu
 * */
if ($user->authorise('core.manage', 'com_menus')) {
  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_MENUS'), '#'), true
  );
  $createMenu = $shownew && $user->authorise('core.create', 'com_menus');

  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_MENU_MANAGER'), 'index.php?option=com_menus&view=menus', 'class:menumgr'), $createMenu
  );

  if ($createMenu) {
    $menu->addChild(
            new JMenuNode(JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU'), 'index.php?option=com_menus&view=menu&layout=edit', 'class:newarticle')
    );
    $menu->getParent();
  }

  $menu->addSeparator();

  // Menu Types
  foreach (ModMenuHelper::getMenus() as $menuType) {
    $alt = '*' . $menuType->sef . '*';

    if ($menuType->home == 0) {
      $titleicon = '';
    } elseif ($menuType->home == 1 && $menuType->language == '*') {
      $titleicon = ' <i class="icon-home"></i>';
    } elseif ($menuType->home > 1) {
      $titleicon = ' <span>' . JHtml::_('image', 'mod_languages/icon-16-language.png', $menuType->home, array('title' => JText::_('MOD_MENU_HOME_MULTIPLE')), true) . '</span>';
    } else {
      $image = JHtml::_('image', 'mod_languages/' . $menuType->image . '.gif', null, null, true, true);

      if (!$image) {
        $titleicon = ' <span>' . JHtml::_('image', 'mod_languages/icon-16-language.png', $alt, array('title' => $menuType->title_native), true) . '</span>';
      } else {
        $titleicon = ' <span>' . JHtml::_('image', 'mod_languages/' . $menuType->image . '.gif', $alt, array('title' => $menuType->title_native), true) . '</span>';
      }
    }

    $menu->addChild(
            new JMenuNode($menuType->title, 'index.php?option=com_menus&view=items&menutype=' . $menuType->menutype, 'class:menu', null, null, $titleicon), $createMenu
    );

    if ($createMenu) {
      $menu->addChild(
              new JMenuNode(JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU_ITEM'), 'index.php?option=com_menus&view=item&layout=edit&menutype=' . $menuType->menutype, 'class:newarticle')
      );
      $menu->getParent();
    }
  }
  $menu->getParent();
}

/**
 * Content Submenu
 * */
if ($user->authorise('core.manage', 'com_content')) {
  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_COM_CONTENT'), '#'), true
  );
  $createContent = $shownew && $user->authorise('core.create', 'com_content');
  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_COM_CONTENT_ARTICLE_MANAGER'), 'index.php?option=com_content', 'class:article'), $createContent
  );

  if ($createContent) {
    $menu->addChild(
            new JMenuNode(JText::_('MOD_MENU_COM_CONTENT_NEW_ARTICLE'), 'index.php?option=com_content&task=article.add', 'class:newarticle')
    );
    $menu->getParent();
  }

  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_COM_CONTENT_CATEGORY_MANAGER'), 'index.php?option=com_categories&extension=com_content', 'class:category'), $createContent
  );

  if ($createContent) {
    $menu->addChild(
            new JMenuNode(JText::_('MOD_MENU_COM_CONTENT_NEW_CATEGORY'), 'index.php?option=com_categories&task=category.add&extension=com_content', 'class:newarticle')
    );
    $menu->getParent();
  }

  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_COM_CONTENT_FEATURED'), 'index.php?option=com_content&view=featured', 'class:featured')
  );
  $menu->addSeparator();

  if ($user->authorise('core.manage', 'com_media')) {
    $menu->addChild(new JMenuNode(JText::_('MOD_MENU_MEDIA_MANAGER'), 'index.php?option=com_media', 'class:media'));
  }

  $menu->getParent();
}



/**
 * Components Submenu
 * */
// Get the authorised components and sub-menus.
$components = ModMenuHelper::getComponents(true);

// Check if there are any components, otherwise, don't render the menu
if ($components) {
  $menu->addChild(new JMenuNode(JText::_('MOD_MENU_COMPONENTS'), '#'), true);

  foreach ($components as &$component) {
    if (!empty($component->submenu)) {
      // This component has a db driven submenu.
      $menu->addChild(new JMenuNode($component->text, $component->link, $component->img), true);

      foreach ($component->submenu as $sub) {
        $menu->addChild(new JMenuNode($sub->text, $sub->link, $sub->img));
      }

      $menu->getParent();
    } else {
      $menu->addChild(new JMenuNode($component->text, $component->link, $component->img));
    }
  }

  $menu->getParent();
}

/**
 * Extensions Submenu
 * */
$im = $user->authorise('core.manage', 'com_installer');
$mm = $user->authorise('core.manage', 'com_modules');
$pm = $user->authorise('core.manage', 'com_plugins');
$tm = $user->authorise('core.manage', 'com_templates');
$lm = $user->authorise('core.manage', 'com_languages');

if ($im || $mm || $pm || $tm || $lm) {
  $menu->addChild(new JMenuNode(JText::_('MOD_MENU_EXTENSIONS_EXTENSIONS'), '#'), true);

  if ($im) {
    $menu->addChild(new JMenuNode(JText::_('MOD_MENU_EXTENSIONS_EXTENSION_MANAGER'), 'index.php?option=com_installer', 'class:install'));
    $menu->addSeparator();
  }

  if ($mm) {
    $menu->addChild(new JMenuNode(JText::_('MOD_MENU_EXTENSIONS_MODULE_MANAGER'), 'index.php?option=com_modules', 'class:module'));
  }

  if ($pm) {
    $menu->addChild(new JMenuNode(JText::_('MOD_MENU_EXTENSIONS_PLUGIN_MANAGER'), 'index.php?option=com_plugins', 'class:plugin'));
  }

  if ($tm) {
    $menu->addChild(new JMenuNode(JText::_('MOD_MENU_EXTENSIONS_TEMPLATE_MANAGER'), 'index.php?option=com_templates', 'class:themes'));
  }

  if ($lm) {
    $menu->addChild(new JMenuNode(JText::_('MOD_MENU_EXTENSIONS_LANGUAGE_MANAGER'), 'index.php?option=com_languages', 'class:language'));
  }

  $menu->getParent();
}

/**
 * Help Submenu
 * */
if ($showhelp == 1) {
  $menu->addChild(
          new JMenuNode(JText::_('MOD_MENU_HELP'), '#'), true
  );
  $menu->addChild(
          new JMenuNode(JText::_('COM_ADMIN_MENU_HELP'), '#', 'class:help', false, '_blank')
  );
  $menu->addSeparator();

  $menu->addChild(
          new JMenuNode(JText::_('COM_ADMIN_CONTACT_US'), '/contact-us', 'class:contact-us', false, '_blank')
  );


  $menu->getParent();
}
