<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$script_path = (JDEBUG) ? '/media/fc/assets/js/scripts.js' : '/media/fc/assets/js/20150508125530.scripts.min.js';
$css_path = (JDEBUG) ? '/media/fc/assets/css/styles.css' : '/media/fc/assets/css/20150508125530.styles.min.css';

$doc->addStyleSheet('//' . $URI->getHost() . $css_path);
$doc->addScript('//' . $URI->getHost() . $script_path, 'text/javascript', false, true);

