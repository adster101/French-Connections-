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

$showhelp = $params->get('showhelp', 1);
$user = JFactory::getUser();

/**
 * Property Submenu
 */
$addRental = $user->authorise('core.create', 'com_rental');


$manage_rental = $user->authorise('core.manage', 'com_rental');
$manage_realestate = $user->authorise('core.manage', 'com_rental');
$manage_offers = $user->authorise('core.manage', 'com_specialoffers');

$manage_account = $user->authorise('core.manage', 'com_invoices');



if ($manage_rental || $manage_realestate)
{

  $menu->addChild(new JMenuNode(JText::_('COM_ADMIN_RENTAL_PROPERTY'), '#', '', $menu->isActive()), true);

  if ($manage_rental)
  {
    $menu->addChild(new JMenuNode(JText::_('COM_RENTAL_MENU'), 'index.php', '', $menu->isActive('index.php?option=com_rental')), true);
    $menu->getParent();
  }
  
  $menu->addChild(new JMenuNode(JText::_('COM_REVIEWS_MENU'), 'index.php?option=com_reviews', '', $menu->isActive('index.php?option=com_reviews')), true);
  $menu->getParent();

  if ($manage_offers)
  {
    $menu->addChild(new JMenuNode(JText::_('COM_SPECIALOFFERS_MENU'), 'index.php?option=com_specialoffers', '', $menu->isActive('index.php?option=com_specialoffers')), true);
    $menu->getParent();
  }

  if ($addRental)
  {
    $menu->addChild(new JMenuNode(JText::_('COM_PROPERTY_CREATE_NEW_RENTAL_PROPERTY'), 'index.php?option=com_rental&task=propertyversions.add', '', $menu->isActive()));
    $menu->getParent();
  }
}

$menu->addChild(new JMenuNode(JText::_('COM_ENQUIRIES_MENU'), 'index.php?option=com_enquiries', 'envelope', $menu->isActive('index.php?option=com_enquiries')), true);
$menu->getParent();



$menu->addChild(new JMenuNode(JText::_('COM_STATS_MENU'), 'index.php?option=com_stats', 'chart', $menu->isActive('index.php?option=com_stats')), true);
$menu->getParent();


/* if ($manage_realestate) {
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
  } */

/**
 * Account menu
 */
if ($manage_account)
{
  $menu->addChild(new JMenuNode(JText::_('COM_ACCOUNTS_MENU'), '#', 'user', $menu->isActive()), true);

  $general = 'index.php?option=com_admin&task=profile.edit&id=' . (int) $user->id . '#tab-general';
  $menu->addChild(new JMenuNode(JText::_('TPL_FCADMIN_ALT_EDIT_ACCOUNT'), $general, '', $menu->isActive($general)));
  $menu->addChild(new JMenuNode(JText::_('TPL_FCADMIN_ALT_EDIT_ACCOUNT_SMS'), 
          'index.php?option=com_admin&task=profile.edit&id=' . (int) $user->id . '#tab-sms', 'mobile', $menu->isActive()));
  $menu->addChild(new JMenuNode(JText::_('Invoice History'), 
          'index.php?option=com_invoices&view=invoices', '', $menu->isActive()), true);

  $menu->getParent();
  $menu->getParent();
}

