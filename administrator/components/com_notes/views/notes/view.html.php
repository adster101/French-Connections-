<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * User notes list view
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class NotesViewNotes extends JViewLegacy
{
	/**
	 * A list of user note objects.
	 *
	 * @var    array
	 * @since  2.5
	 */
	protected $items;

	/**
	 * The pagination object.
	 *
	 * @var    JPagination
	 * @since  2.5
	 */
	protected $pagination;

	/**
	 * The model state.
	 *
	 * @var    JObject
	 * @since  2.5
	 */
	protected $state;

	/**
	 * The model state.
	 *
	 * @var    JUser
	 * @since  2.5
	 */
	protected $user;

	/**
	 * Override the display method for the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   2.5
	 */
	public function display($tpl = null)
	{
		// Initialise view variables.
		$this->items      = $this->get('Items');
		$this->state      = $this->get('State');
		$this->user       = $this->get('User');
    $this->pagination = $this->get('Pagination');

    $this->id = JFactory::getApplication()->input->get('property_id','','int');

    // Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Display the toolbar.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function addToolbar()
	{
    
    $user = JFactory::getUser();

		JToolbarHelper::title(JText::_('COM_NOTES_VIEW_NOTES_TITLE'), 'list-view');

		if ($user->authorise('core.create','com_notes'))
		{
			JToolbarHelper::addNew('note.add');
		}

		if ($user->authorise('core.edit'))
		{
			JToolbarHelper::editList('note.edit');
		}

		if ($this->state->get('filter.state') == -2 && $user->authorise('core.delete','com_notes'))
		{
			JToolbarHelper::deleteList('', 'notes.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolbarHelper::divider();
		}
		elseif ($user->authorise('core.edit.state','com_notes'))
		{
			JToolbarHelper::trash('notes.trash');
			JToolbarHelper::divider();
		}

		if ($user->authorise('core.admin', 'com_notes'))
		{
			JToolbarHelper::preferences('com_notes');
			JToolbarHelper::divider();
		}
		JToolbarHelper::help('JHELP_USERS_USER_NOTES');
	}
}
