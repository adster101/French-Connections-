<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.modal');

/**
 * View class for a list of messages.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 * @since       1.6
 */
class FcMessagesViewfcMessages extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();

    parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= JHelperContent::getActions('com_fcmessages');

		JToolbarHelper::title(JText::_('COM_FCMESSAGES_MANAGER_MESSAGES'), 'envelope');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('fcmessage.add');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::divider();
			JToolbarHelper::publish('fcmessages.publish', 'COM_FCMESSAGES_TOOLBAR_MARK_AS_READ');
			JToolbarHelper::unpublish('fcmessages.unpublish', 'COM_FCMESSAGES_TOOLBAR_MARK_AS_UNREAD');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::divider();
			JToolbarHelper::deleteList('', 'fcmessages.delete', 'JTOOLBAR_EMPTY_TRASH');
		} elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::divider();
			JToolbarHelper::trash('fcmessages.trash');
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_fcmessages');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_MESSAGING_INBOX', true);
	}
}
