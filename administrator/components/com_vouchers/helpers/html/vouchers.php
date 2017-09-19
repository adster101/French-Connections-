<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
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
class JHtmlVouchers
{
	/**
	 * @param	int $value	The state value
	 * @param	int $i
	 */
	public static function state($value, $i, $enabled = true, $checkbox = 'cb')
	
	{
		$states = array(
			1 => array(
				'unpublish',
				'COM_BANNERS_BANNERS_PINNED',
				'COM_BANNERS_BANNERS_HTML_PIN_BANNER',
				'COM_BANNERS_BANNERS_PINNED',
				true,
				'publish',
				'publish'
			),
			0 => array(
				'publish',
				'COM_BANNERS_BANNERS_UNPINNED',
				'COM_BANNERS_BANNERS_HTML_UNPIN_BANNER',
				'COM_BANNERS_BANNERS_UNPINNED',
				true,
				'unpublish',
				'unpublish'
			),
		);

		return JHtml::_('jgrid.state', $states, $value, $i, 'vouchers.', $enabled, true, $checkbox);
	}
}
