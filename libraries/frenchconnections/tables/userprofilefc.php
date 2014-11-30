<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * User notes table class
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class RentalTableUserProfileFc extends JTable
{
	/**
	 * Constructor
	 *
	 * @param  JDatabaseDriver  &$db  Database object
	 *
	 * @since  2.5
	 */
	public function __construct($db)
	{
		parent::__construct('#__user_profile_fc', 'user_id', $db);
	}
}
