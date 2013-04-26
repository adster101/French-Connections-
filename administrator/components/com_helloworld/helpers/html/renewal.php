<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 * @since       1.6
 */
class JHtmlRenewal
{
	/**
	 * @param	int $value	The state value
	 * @param	int $i
	 */
	public static function state($value = 0, $i, $canChange)
	{
		// Array of image, task, title, action.
    // Possible renewal states are
    // Expired (renew now)
    // About to expire (non auto renew) (renew now) (opt in)
    // About to expire (auto renew) (opt out)
    // Publshed with > 28 days to renewal (non auto renew) (opt in)
 
    
		$states	= array(
			0	=> array('publish_x.png',		'helloworlds.autorenewon',	'COM_MESSAGES_OPTION_READ',		'COM_MESSAGES_MARK_AS_UNREAD'),
			1	=> array('publish_g.png',	'enquiries.autorenewoff',		'COM_MESSAGES_OPTION_UNREAD',	'COM_MESSAGES_MARK_AS_READ')
		);
    
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
    
		$html	= JHtml::_('image', 'admin/'.$state[0], JText::_($state[2]), null, true);
    
		if ($canChange) {
      
			$html = '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					.$html.'</a>';
		}

		return $html;
	}
}
