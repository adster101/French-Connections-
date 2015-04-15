<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_footer
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$doc = JFactory::getDocument();

/*
 * Javascript to insert the link
 * View element calls jSelectArticle when an article is clicked
 * jSelectArticle creates the link tag, sends it to the editor,
 * and closes the select frame.
 */

$js = "(function(){
    var c = document.createElement('script');
    c.type = 'text/javascript'; c.async = true;
    c.src = '//frenchconnections.smartertrack.com/ChatLink.ashx?config=1&id=stlivechat21';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(c,s);
  })();";

JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');

$rental_model = JModelLegacy::getInstance('Listings', 'RentalModel');

$items = $rental_model->getItems();


// Add car trawler scripts here...
$doc->addScriptDeclaration($js);

require JModuleHelper::getLayoutPath('mod_livechat', $params->get('layout', 'default'));
