<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$lang = JFactory::getLanguage();
$lang->load('com_accommodation', JPATH_SITE, null, false, true);

$doc = JFactory::getDocument();
$app = JFactory::getApplication();

$uri = JUri::current();

if ($this->results === false || $this->total == 0) { 
  echo $this->loadTemplate('no_results');
} else {
  echo $this->loadTemplate('results');
}