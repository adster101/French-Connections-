<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;



// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

// Set the input var so we can pick it up in the contact model
$app  = JFactory::getApplication();
$app->input->set('askus', true);

// Load the contact language string
$lang = JFactory::getLanguage();
$lang->load('com_fccontact', JPATH_BASE);

// Add the model and form include paths
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_fccontact/models');
JForm::addFormPath(JPATH_SITE . '/components/com_fccontact/models/forms');

// Get the contact model
$model = JModelLegacy::getInstance('Contact','FcContactModel');

// Load the form info
$form = $model->getForm();

require JModuleHelper::getLayoutPath('mod_askus', $params->get('layout', 'default'));
