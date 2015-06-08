<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_status
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$hideLinks = false;
$task      = $input->getCmd('task');
$output    = array();
?>
You have 
<?php //  Print the inbox message.
if ($params->get('show_messages', 1))
{
	$active = $unread ? ' badge-warning' : '';
	$output[] = '<div class="btn-group hasTooltip ' . $inboxClass . '"'
		. ' title="' . JText::plural('MOD_STATUS_MESSAGES', $unread) . '"'
		. ' alt="' . JText::plural('MOD_STATUS_MESSAGES', $unread) . '">'
		. ($hideLinks ? '' : '<a href="' . $inboxLink . '">')
		. '<i class="icon icon-envelope">&nbsp;</i> '
		. '<span class="badge' . $active . '">' . $unread . '</span>'
		. ($hideLinks ? '' : '</a>')
		. '<div class="btn-group divider"></div>'
		. '</div>';
}

// Output the items.
foreach ($output as $item)
{
	echo $item;
}
?>
unread messages.

