<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Indexer view class for Finder.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 * @since       2.5
 */
class RentalViewNote extends JViewLegacy
{
	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function display($tpl = null)
	{
 		// Initialise view variables.
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Get the component HTML helpers
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

		parent::display($tpl);
		$this->addToolbar();
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		// Determine the view we are using.
		$view = strtolower(JRequest::getVar('view'));


    // Show a helpful toobar title
    JToolBarHelper::title(JText::_('COM_RENTAL_HELLOWORLD_ADD_NOTE'));

    JToolBarHelper::save('note.save', 'JTOOLBAR_SAVE');

    JToolBarHelper::cancel('note.cancel', 'JTOOLBAR_CANCEL');
	}

  /**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_RENTAL_HELLOWORLD_ADD_NOTE'));
    $document->addScript(JURI::root() . "/administrator/components/com_rental/js/submitbutton.js", 'text/javascript',true, false);
  }




}
