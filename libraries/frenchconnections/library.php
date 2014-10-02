<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Ensure that autoloaders are set
JLoader::setup();

JLoader::registerPrefix('fc', dirname(__FILE__));

// Register the global PropertyHelper class
JLoader::register('PropertyHelper', dirname(__FILE__) . '/helpers/property.php');

// Register the global SearchHelper class
JLoader::register('SearchHelper', dirname(__FILE__) . '/helpers/search.php');

// Common HTML helpers
JHtml::addIncludePath(dirname(__FILE__) . '/helpers/html');

// Register the Preview button
JLoader::register('JToolbarButtonPreview', dirname(__FILE__) . '/buttons/preview.php'); 

// Load library language
$lang = JFactory::getLanguage();
$lang->load('frenchconnections', JPATH_SITE . '/libraries/frenchconnections');