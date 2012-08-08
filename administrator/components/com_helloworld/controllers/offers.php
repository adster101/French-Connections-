<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controlleradmin');

/**
 * Offers Controller
 */
class HelloWorldControllerOffers extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Offers', $prefix = 'HelloWorldModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
