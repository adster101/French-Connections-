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

$lang = JFactory::getLanguage();
$app  = JFactory::getApplication();
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_fccontact/models');
$paths = JForm::addFormPath(JPATH_SITE . '/components/com_fccontact/models/forms');

$lang->load('com_fccontact', JPATH_BASE);

$model = JModelLegacy::getInstance('Contact','FcContactModel');

$form = $model->getForm('askus');

require JModuleHelper::getLayoutPath('mod_askus', $params->get('layout', 'default'));
