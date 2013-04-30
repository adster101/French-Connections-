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
			0	=> array(
          'chevron-right',	
          'autorenewals.showtransactionlist', 
          'Enable auto renewals for this listing', 
          'Enable auto-renewals',
          'Click here to enable auto renewals for this listing.'
          ),
			1	=> array(
          'chevron-right',
          'autorenewals.showtransactionlist',
          'Auto renewals have been enabled for this listing.',
          'Cancel auto-renewals', 
          'This property will automatically renew on the expiry date shown. No further action requried etc')
		);
    
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
        
		if ($canChange) {
      $html = '<p class=\'small\'>' . $state[2] . '</p>';
			$html .= '<a rel="tooltip" class="btn" href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[4]).'">';
      $html .= $state[3];
      $html .= '<i class=\'icon-' . $state[0] . '\'></i>';
      $html .='</a>';
		}

		return $html;
	}
}
