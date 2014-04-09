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
$addRental = $user->authorise('core.create', 'com_rental');
$addRealestate = $user->authorise('core.create', 'com_rental');
$addOffer = $user->authorise('core.create', 'com_specialoffers');
$addReview = $user->authorise('core.create', 'com_reviews');

$manage_rental = $user->authorise('core.manage', 'com_rental');
$manage_realestate = $user->authorise('core.manage', 'com_rental');
$manage_offers = $user->authorise('core.manage', 'com_specialoffers');

$manage_account = $user->authorise('core.manage', 'com_invoices');

$manage_users = $user->authorise('core.manage', 'com_users');

$manage_vouchers = $user->authorise('core.manage', 'com_vouchers');



//$menu->addChild(new JMenuNode(JText::_('COM_ADMIN_HOME'), 'index.php'), true);
//$menu->getParent();

if ($manage_rental || $manage_realestate) {

  $menu->addChild(new JMenuNode(JText::_('COM_ADMIN_RENTAL_PROPERTY'), '#'), true);

  if ($manage_rental) {

    $menu->addChild(new JMenuNode(JText::_('COM_RENTAL_MENU'), 'index.php?option=com_rental', 'class:property'), true);
    $menu->addChild(new JMenuNode(JText::_('COM_RENTAL_VIEW_ALL_MENU'), 'index.php?option=com_rental', 'class:property'));

    if ($addRental) {

      $menu->addChild(
              new JMenuNode(JText::_('COM_PROPERTY_CREATE_NEW_RENTAL_PROPERTY'), 'index.php?option=com_rental&task=propertyversions.add', 'class:newproperty')
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

  $menu->addChild(new JMenuNode(JText::_('Statistics'), 'index.php?option=com_stats', 'class:stats'));

  $menu->getParent();
}
$menu->addSeparator();


$menu->addChild(
        new JMenuNode(JText::_('COM_ENQUIRIES_MENU'), 'index.php?option=com_enquiries', 'class:enquiries'), true
);
$menu->getParent();


/**
 * Account menu
 */
if ($manage_account) {

  $menu->addChild(
          new JMenuNode(JText::_('COM_ACCOUNTS_MENU'), '#'), true
  );


    $menu->addChild(new JMenuNode(JText::_('TPL_ISIS_EDIT_ACCOUNT'), 'index.php?option=com_admin&task=profile.edit&id=' . (int) $user->id, 'class:accounts'));
    $menu->addSeparator();
  


  $menu->addChild(new JMenuNode(JText::_('Invoice History'), 'index.php?option=com_invoices&view=invoices', 'class:accounts'), true);

  $menu->getParent();


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
