<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$script_path = (JDEBUG) ? '/media/fc/js/scripts.js' : '/media/fc/assets/js/20160919115919.scripts.min.js';
$css_path = (JDEBUG) ? '/media/fc/css/styles.css' : '/media/fc/assets/css/20160919115919.styles.min.css';

//$doc->addStyleSheet('//' . $URI->getHost() . $css_path);
$doc->addScript('//' . $URI->getHost() . $script_path, 'text/javascript', true, true);

