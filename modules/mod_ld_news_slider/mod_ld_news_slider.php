<?php

/*
 * Helper file for LD News Slider
 * @package Joomla!
 * @Copyright (C) 2010 littledonkey.net
 * @ All rights reserved
 * @ Joomla! is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.2.0 $
*/


defined('_JEXEC') or die('Restricted access');

/*
 * Loading helper class
*/

require_once (dirname(__FILE__).DS.'helper.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

$helper = new ModLDnewsSliderHelper();

$helper->getArticles($params);

$helper->renderLayout($params);

?>
