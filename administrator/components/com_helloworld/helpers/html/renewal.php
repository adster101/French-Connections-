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
	public static function state($value = '', $i, $canChange)
	{
		// Array of image, task, title, action.
    // Possible renewal states are
    // Expired (renew now)
    // About to expire (non auto-renew) (renew now) (opt in)
    // About to expire (auto renew) (opt out)
    // Publshed with > 28 days to renewal (non auto renew) (opt in)
 
    
		$states	= array(
			0	=> array('cogs',		'property.setupautorenewal',	'Enable automatic renewals for this listing', 'Why not enable auto renewals...?'),
			1	=> array('wrench',	'property.setupautorenewal',		'Automatic renewals have been enabled for this listing', 'Auto renewals have been enabled for this listing. This property will automatically renew on the expiry date shown. No further action requried etc etc blah blah blah')
		);
    
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
    
    
		if ($canChange) {
			$html = '<a rel="tooltip" class="btn" href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">';
      $html .= $state[2];
      $html .='</a>';
		}

		return $html;
	}
}
