<?php

/**
 * OpenX SPC entry point file for OpenX Single Page Call Module
 * 
 * @package    SysgenMedia OpenX Single Page Call
 * @subpackage Modules
 * @copyright	Copyright (c)2010 Sysgen Media LLC. All Rights Reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software and parts of it may contain or be derived from the
 * GNU General Public License or other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$zoneID = $params->get('openx_zone_id');
$location = $params->get('openx_location');


$doc =& JFactory::getDocument();
$doc->addScript("http://".$location);


echo "

<script type=\"text/javascript\">
<!--// <![CDATA[
    OA_show($zoneID);
// ]]> -->
</script>

";

?>