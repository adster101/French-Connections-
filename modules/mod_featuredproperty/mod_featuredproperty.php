<?php
 
/**
 * @package     Joomla.Tutorials
 * @subpackage  Module
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die;

require_once (dirname(__FILE__).DS.'helper.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');



$helper = new modFeaturedPropertyHelper();

$helper->getFeaturedProperties();

$helper->renderLayout($params);

