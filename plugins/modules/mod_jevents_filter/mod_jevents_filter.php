<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: mod_jevents_filter.php 941 2010-05-20 13:21:57Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Filter
 * @copyright   Copyright (C) 2008 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.gwesystems.com
 */


defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (dirname(__FILE__).'/'.'helper.php');

$jevhelper = new modJeventsFilterHelper($params);

// record what is running - used by the filters
$registry	=& JRegistry::getInstance("jevents");
$registry->set("jevents.activeprocess","mod_jevents_filter");
$registry->set("jevents.moduleid", $module->id);
$registry->set("jevents.moduleparams", $params);

require(JModuleHelper::getLayoutPath('mod_jevents_filter'));

