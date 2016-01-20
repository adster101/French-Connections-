<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if ($view == 'images' && ($option == 'com_rental' || $option == 'com_realestate'))
{
<<<<<<< HEAD
  $script_path = (JDEBUG) ? '/media/fc/js/images.admin.scripts.js' : '/media/fc/assets/js/20160120114718.images.admin.scripts.min.js';
}
else
{
  $script_path = (JDEBUG) ? '/media/fc/js/admin.scripts.js' : '/media/fc/assets/js/20160120114718.admin.scripts.min.js';
=======
  $script_path = (JDEBUG) ? '/media/fc/js/images.admin.scripts.js' : '/media/fc/assets/js/20160120142832.images.admin.scripts.min.js';
}
else
{
  $script_path = (JDEBUG) ? '/media/fc/js/admin.scripts.js' : '/media/fc/assets/js/20160120142832.admin.scripts.min.js';
>>>>>>> release-3.1.3
}

//$doc->addStyleSheet('//' . $URI->getHost() . $css_path);
$doc->addScript('//' . $uri->getHost() . $script_path);

