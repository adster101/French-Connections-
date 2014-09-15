<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Ensure that autoloaders are set
JLoader::setup();

JLoader::registerPrefix('Fc', dirname(__FILE__));

// Register the global PropertyHelper class
JLoader::register('PropertyHelper', dirname(__FILE__) . '/helpers/property.php');

// Common HTML helpers
JHtml::addIncludePath(dirname(__FILE__) . '/helpers/html');

// Register the Preview button
JLoader::register('JToolbarButtonPreview', dirname(__FILE__) . '/buttons/preview.php'); 