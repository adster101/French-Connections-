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
$uri = JUri::getInstance();

//	looking for scripts
$headers = $doc->getHeadData();
$scripts = isset($headers['scripts']) ? $headers['scripts'] : array();
//	cleare the original scripts
$headers['scripts'] = array();
$headers['script'] = array();

//	This removes all scripts from the head, conflicts with the booking engine ensue otherwise
foreach ($scripts as $url => $type) {

  $relativePath = trim(str_replace($uri->getPath(), '', JUri::root()), '/');
  $relativeScript = trim(str_replace($uri->getPath(), '', $url), '/');
  $relativeUrl = str_replace($relativePath, '', $url);

  // Try to disable relative and full URLs
  unset($doc->_scripts[$url]);
  unset($doc->_scripts[$relativeUrl]);
  unset($doc->_scripts[JUri::root(true) . $url]);
  unset($doc->_scripts[$relativeScript]);
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_car_trawler', $params->get('layout', 'default'));
