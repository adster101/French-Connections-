<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * HelloWorld Controller
 */
class HelloWorldControllerAvailability extends JControllerForm
{
	/*
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Availability', $prefix = 'HelloWorldModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
