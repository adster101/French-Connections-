<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';


$lang = JFactory::getLanguage();

$lang->load('com_fcsearch', JPATH_SITE, null, false, true);

$app  = JFactory::getApplication();

$upper_limit = $lang->getUpperLimitSearchWord();

$button			= $params->get('button', '');
$imagebutton	= $params->get('imagebutton', '');
$button_pos		= $params->get('button_pos', 'left');
$button_text	= htmlspecialchars($params->get('button_text', JText::_('MOD_SEARCH_SEARCHBUTTON_TEXT')));
$width			= (int) $params->get('width', 20);
$maxlength		= $upper_limit;
$text			= htmlspecialchars($params->get('text', JText::_('MOD_SEARCH_SEARCHBOX_TEXT')));
$label			= htmlspecialchars($params->get('label', JText::_('MOD_SEARCH_LABEL_TEXT')));
$set_Itemid		= (int) $params->get('set_itemid', 0);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

if ($imagebutton) {
	$img = modSearchHelper::getSearchImage($button_text);
}
$mitemid = $set_Itemid > 0 ? $set_Itemid : $app->input->get('Itemid');



require JModuleHelper::getLayoutPath('mod_fc_search', $params->get('layout', 'default'));

$document = JFactory::getDocument();

$document->addScript(JURI::root() . 'media/fc/js/jquery-ui-1.8.23.custom.min.js','text/javascript');
$document->addScript(JURI::root() . 'media/fc/js/date-range.js','text/javascript', true);
$document->addScript(JURI::root() . 'media/fc/js/search.js','text/javascript', true);

$document->addStyleSheet(JURI::root() . 'media/fc/css/jquery-ui-1.8.23.custom.css');

