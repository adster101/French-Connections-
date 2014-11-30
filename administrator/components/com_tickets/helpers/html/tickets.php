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
class JHtmlTickets
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
				'COM_TICKETS_UNPUBLISH',
				'COM_TICKETS_UNPUBLISH',
				'COM_TICKETS_UNPUBLISH',
				true,
				'publish',
				'publish'
			),
			0 => array(
				'publish',
				'COM_TICKETS_PUBLISH',
				'COM_TICKETS_PUBLISH',
				'COM_TICKETS_PUBLISH',
				true,
				'unpublish',
				'unpublish'
			),
      2 => array(
          'testing',
          'COM_TICKETS_TESTING',
          'COM_TICKETS_TESTING',
          'COM_TICKETS_TESTING',
          true,
          'pending',
          'pending'
      ),
      3 => array(
          'pending',
          'COM_TICKETS_PENDING',
          'COM_TICKETS_PENDING',
          'COM_TICKETSs_PENDING',
          true,
          'help',
          'help'
      ),
        
        
		);

		return JHtml::_('jgrid.state', $states, $value, $i, 'tickets.', $enabled, true, $checkbox);
	}
}
