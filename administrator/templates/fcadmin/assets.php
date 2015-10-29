<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if ($view == 'images' && ($option == 'com_rental' || $option == 'com_realestate'))
{
<<<<<<< HEAD
  $script_path = (JDEBUG) ? '/media/fc/js/images.admin.scripts.js' : '/media/fc/assets/js/20151026165818.images.admin.scripts.min.js';
}
else
{
  $script_path = (JDEBUG) ? '/media/fc/js/admin.scripts.js' : '/media/fc/assets/js/20151026165818.admin.scripts.min.js';
=======
  $script_path = (JDEBUG) ? '/media/fc/js/images.admin.scripts.js' : '/media/fc/assets/js/20151028133258.images.admin.scripts.min.js';
}
else
{
  $script_path = (JDEBUG) ? '/media/fc/js/admin.scripts.js' : '/media/fc/assets/js/20151028133258.admin.scripts.min.js';
>>>>>>> release-2.8
}

//$doc->addStyleSheet('//' . $URI->getHost() . $css_path);
$doc->addScript('//' . $uri->getHost() . $script_path);

