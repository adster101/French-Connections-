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
 * Extended Utility class for the Users component.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class JHtmlProperty
{
	/**
	 * Display an image.
	 *
	 * @param   string  $src  The source of the image
	 *
	 * @return  string  A <img> element if the specified file exists, otherwise, a null string
	 *
	 * @since   2.5
	 */
	public static function image($src)
	{
		$src = preg_replace('#[^A-Z0-9\-_\./]#i', '', $src);
		$file = JPATH_SITE . '/' . $src;

		jimport('joomla.filesystem.path');
		JPath::check($file);

		if (!file_exists($file))
		{
			return '';
		}

		return '<img src="' . JUri::root() . $src . '" alt="" />';
	}

	/**
	 * Displays an icon to add a note for this user.
	 *
	 * @param   integer  $userId  The user ID
	 *
	 * @return  string  A link to add a note
	 *
	 * @since   2.5
	 */
	public static function addNote($userId)
	{
		$title = JText::_('COM_USERS_ADD_NOTE');

		return '<a href="' . JRoute::_('index.php?option=com_users&task=note.add&u_id=' . (int) $userId) . '">'
			. '<span class="label label-info"><i class="icon-vcard"></i>' . $title . '</span></a>';
	}

	/**
	 * Displays an icon to filter the notes list on this user.
	 *
	 * @param   integer  $count   The number of notes for the user
	 * @param   integer  $userId  The user ID
	 *
	 * @return  string  A link to apply a filter
	 *
	 * @since   2.5
	 */
	public static function filterNotes($count, $userId)
	{
		if (empty($count))
		{
			return '';
		}

		$title = JText::_('COM_USERS_FILTER_NOTES');

		return '<a href="' . JRoute::_('index.php?option=com_users&view=notes&filter_search=uid:' . (int) $userId) . '">'
			. JHtml::_('image', 'admin/filter_16.png', 'COM_USERS_NOTES', array('title' => $title), true) . '</a>';
	}

	/**
	 * Displays a note icon.
	 *
	 * @param   integer  $count   The number of notes for the user
	 * @param   integer  $userId  The user ID
	 *
	 * @return  string  A link to a modal window with the user notes
	 *
	 * @since   2.5
	 */
	public static function notes($id)
	{
		if (empty($id))
		{
			return '';
		}

		$title = JText::_('COM_HELLOWORLD_HELLOWORLD_VIEW_PROPERTY_NOTES');

		return '<a class="modal"'
			. ' href="' . JRoute::_('index.php?option=com_helloworld&view=notes&tmpl=component&layout=modal&property_id=' . (int) $id) . '"'
			. ' rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
			. '<span class="label label-info"><i class="icon-drawer-2"></i>' . $title . '</span></a>';
	}

	/**
	 * Build an array of block/unblock user states to be used by jgrid.state,
	 * State options will be different for any user
	 * and for currently logged in user
	 *
	 * @param   boolean  $self  True if state array is for currently logged in user
	 *
	 * @return  array  a list of possible states to display
	 *
	 * @since  3.0
	 */
	public static function blockStates( $self = false)
	{
		if ($self)
		{
			$states = array(
				1 => array(
					'task'				=> 'unblock',
					'text'				=> '',
					'active_title'		=> 'COM_USERS_USER_FIELD_BLOCK_DESC',
					'inactive_title'	=> '',
					'tip'				=> true,
					'active_class'		=> 'unpublish',
					'inactive_class'	=> 'unpublish'
				),
				0 => array(
					'task'				=> 'block',
					'text'				=> '',
					'active_title'		=> '',
					'inactive_title'	=> 'COM_USERS_USERS_ERROR_CANNOT_BLOCK_SELF',
					'tip'				=> true,
					'active_class'		=> 'publish',
					'inactive_class'	=> 'publish'
				)
			);
		}
		else
		{
			$states = array(
				1 => array(
					'task'				=> 'unblock',
					'text'				=> '',
					'active_title'		=> 'COM_USERS_TOOLBAR_UNBLOCK',
					'inactive_title'	=> '',
					'tip'				=> true,
					'active_class'		=> 'unpublish',
					'inactive_class'	=> 'unpublish'
				),
				0 => array(
					'task'				=> 'block',
					'text'				=> '',
					'active_title'		=> 'COM_USERS_USER_FIELD_BLOCK_DESC',
					'inactive_title'	=> '',
					'tip'				=> true,
					'active_class'		=> 'publish',
					'inactive_class'	=> 'publish'
				)
			);
		}

		return $states;
	}

	/**
	 * Build an array of activate states to be used by jgrid.state,
	 *
	 * @return  array  a list of possible states to display
	 *
	 * @since  3.0
	 */
	public static function activateStates()
	{
		$states = array(
			1	=> array(
				'task'				=> 'activate',
				'text'				=> '',
				'active_title'		=> 'COM_USERS_TOOLBAR_ACTIVATE',
				'inactive_title'	=> '',
				'tip'				=> true,
				'active_class'		=> 'unpublish',
				'inactive_class'	=> 'unpublish'
			),
			0	=> array(
				'task'				=> '',
				'text'				=> '',
				'active_title'		=> '',
				'inactive_title'	=> 'COM_USERS_ACTIVATED',
				'tip'				=> true,
				'active_class'		=> 'publish',
				'inactive_class'	=> 'publish'
			)
		);
		return $states;
	}
	
  /**
	 * @param	int $value	The state value
	 * @param	int $i
	 */
	public static function state($value = 0, $i, $canChange)
	{
		// Array of image, task, title, action.
		$states	= array(
			-2	=> array('trash.png',		'enquiries.unpublish',	'JTRASHED',				'COM_MESSAGES_MARK_AS_UNREAD'),
			1	=> array('email-read-32.png',		'enquiries.unpublish',	'COM_MESSAGES_OPTION_READ',		'COM_MESSAGES_MARK_AS_UNREAD'),
			0	=> array('email-unread-32.png',	'enquiries.publish',		'COM_MESSAGES_OPTION_UNREAD',	'COM_MESSAGES_MARK_AS_READ')
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
		$html	= JHtml::_('image', 'admin/'.$state[0], JText::_($state[2]), null, true);
		if ($canChange) {
			$html = '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					.$html.'</a>';
		}

		return $html;
	}
  
  /**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string	$extension	The extension.
	 * @param   integer  $categoryId	The category ID.
	 *
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getActions($extension, $categoryId = 0)
	{
		$user		= JFactory::getUser();
		$result		= new JObject;
		$parts		= explode('.', $extension);
		$component	= $parts[0];

		if (empty($categoryId))
		{
			$assetName = $component;
			$level = 'component';
		}
		else
		{
			$assetName = $component.'.category.'.(int) $categoryId;
			$level = 'category';
		}

		$actions = JAccess::getActions($component, $level);

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}
  
}
