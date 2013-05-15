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
class HelloWorldViewSnooze extends JViewLegacy
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

    $app = JFactory::getApplication();
    $input = $app->input;

    $this->id = $input->get('id','','int');
    // Get the form for this puppy...
		$form = $this->get('Form');
    $item = $this->get('Item');

    // Assign the view data
    $this->form = $form;
    $this->item = $item;

    $this->setDocument();
    $this->addToolBar();

		parent::display();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		// Determine the view we are using.
		$view = strtolower(JRequest::getVar('view'));


    // Show a helpful toobar title
    JToolBarHelper::title(JText::sprintf('COM_HELLOWORLD_HELLOWORLD_ADD_NOTE',$this->id));

    JToolBarHelper::save('snooze.save', 'JTOOLBAR_SAVE');

    JToolBarHelper::cancel('snooze.cancel', 'JTOOLBAR_CLOSE');
	}

  /**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::sprintf('COM_HELLOWORLD_HELLOWORLD_ADD_NOTE',$this->id));
	}




}
