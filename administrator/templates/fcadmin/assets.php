<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if ($view == 'images' && ($option == 'com_rental' || $option == 'com_realestate'))
{
  $script_path = (JDEBUG) ? '/media/fc/js/images.admin.scripts.js' : '/media/fc/assets/js/20151029151818.images.admin.scripts.min.js';
}
else
{
  $script_path = (JDEBUG) ? '/media/fc/js/admin.scripts.js' : '/media/fc/assets/js/20151029151818.admin.scripts.min.js';
}

//$doc->addStyleSheet('//' . $URI->getHost() . $css_path);
$doc->addScript('//' . $uri->getHost() . $script_path);

