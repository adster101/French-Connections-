<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$URI = JURI::getInstance();
$doc = JFactory::getDocument();

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
if ($view == 'images' && $option == 'com_rental')
{
  $script_path = (JDEBUG) ? '/media/fc/assets/js/images.admin.scripts.js' : '/media/fc/assets/js/2015072316826.admin.scripts.min.js';
}
else
{
  $script_path = (JDEBUG) ? '/media/fc/assets/js/admin.scripts.js' : '/media/fc/assets/js/2015072316826.admin.scripts.min.js';
}
$css_path = (JDEBUG) ? '/media/fc/assets/css/admin.styles.css' : '/media/fc/assets/css/2015072316826.admin.styles.min.css';

//$doc->addStyleSheet('//' . $URI->getHost() . $css_path);
$doc->addScript('//' . $URI->getHost() . $script_path);

