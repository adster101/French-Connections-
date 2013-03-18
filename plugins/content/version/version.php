<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.joomla
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Example Content Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.joomla
 * @since       1.6
 */
class plgContentVersion extends JPlugin
{
	/**
	 * Example after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param   string  The context of the content passed to the plugin (added in 1.6)
	 * @param   object		A JTableContent object
	 * @param   bool		If the content is just about to be created
	 * @since   1.6
	 */
	public function onContentBeforeBind($context, $article, $isNew, $data)
	{

    print_r($data);die;
    
		// Check we are handling the frontend edit form.
		if ($context != 'com_helloworld.unit')
		{
			return true;
		}

		// Check if this function is enabled.
		if (!$this->params->def('email_new_fe', 1))
		{
			return true;
		}

		// Check this is a new article.
		if (!$isNew)
		{
			return true;
		}

		$user = JFactory::getUser();

		// Messaging for new items
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_messages/models', 'MessagesModel');
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_messages/tables');

		$db = JFactory::getDbo();
		$db->setQuery('SELECT id FROM #__users WHERE sendEmail = 1');
		$users = (array) $db->loadColumn();

		$default_language = JComponentHelper::getParams('com_languages')->get('administrator');
		$debug = JFactory::getConfig()->get('debug_lang');

		foreach ($users as $user_id)
		{
			if ($user_id != $user->id)
			{
				// Load language for messaging
				$receiver = JUser::getInstance($user_id);
				$lang = JLanguage::getInstance($receiver->getParam('admin_language', $default_language), $debug);
				$lang->load('com_content');
				$message = array(
					'user_id_to'	=> $user_id,
					'subject'		=> $lang->_('COM_CONTENT_NEW_ARTICLE'),
					'message'		=> sprintf($lang->_('COM_CONTENT_ON_NEW_CONTENT'), $user->get('name'), $article->title)
				);
				$model_message = JModelLegacy::getInstance('Message', 'MessagesModel');
				$result = $model_message->save($message);
			}
		}

		return $result;
	}


}
