<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('frenchconnections.controllers.property.base');
 
/**
 * HelloWorld Controller
 */
class HelloWorldControllerTariffs extends HelloWorldControllerBase
{
  
	protected $extension;

	/**
	 * Constructor.
	 *
	 * @param  array  $config  An optional associative array of configuration settings.
	 *
	 * @since  1.6
	 * @see    JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->extension))
		{
			$this->extension = JRequest::getCmd('extension', 'com_helloworld');
		}
	}

}
