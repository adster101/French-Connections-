<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
<<<<<<< HEAD
$script_path = (JDEBUG) ? '/media/fc/js/scripts.js' : '/media/fc/assets/js/20160120114718.scripts.min.js';
$css_path = (JDEBUG) ? '/media/fc/css/styles.css' : '/media/fc/assets/css/20160120114718.styles.min.css';
=======
$script_path = (JDEBUG) ? '/media/fc/js/scripts.js' : '/media/fc/assets/js/20160120142832.scripts.min.js';
$css_path = (JDEBUG) ? '/media/fc/css/styles.css' : '/media/fc/assets/css/20160120142832.styles.min.css';
>>>>>>> release-3.1.3

//$doc->addStyleSheet('//' . $URI->getHost() . $css_path);
$doc->addScript('//' . $URI->getHost() . $script_path, 'text/javascript', true, true);

