<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Messages Component Message Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 * @since       1.6
 */
class FcMessagesControllerFcMessage extends JControllerForm
{
	/**
	 * Method (override) to check if you can save a new or existing record.
	 *
	 * Adjusts for the primary key name and hands off to the parent class.
	 *
	 * @param   array  An array of input data.
	 * @param   string	The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function allowSave($data, $key = 'message_id')
	{
		return parent::allowSave($data, $key);
	}
  
}