<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewImages extends JViewLegacy
{
	/**
	 * display method of Availability View
	 * @return void
	 */
	public function display($tpl = null)
	{

    // Get the property ID we are editing.
		$this->item->id = JRequest::getVar('id');
    $app = JFactory::getApplication();

    // Get the item data
    $items = $this->get('Items');

    // Assign the Item
		$this->items = $items;

    // Get the state
    $this->state = $this->get('State');

		// Display the template
		parent::display($tpl);

	}

}
