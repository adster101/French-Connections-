<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewStats extends JViewLegacy
{
	/**
	 * display method of Availability View
	 * @return void
	 */
	public function display($tpl = null)
	{

    // Get the model instance
    $this->get('State');

    // Get an instance of the property model
    $this->data = $this->get('Graph');

		// Display the template
		parent::display($tpl);

	}


}
