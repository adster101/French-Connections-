<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Login Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_login
 * @since       1.5
 */
class AutoLoginController extends JControllerLegacy
{
	

	/**
	 * Method to log in a user.
	 *
	 * @return  void
	 */
	public function login()
	{
        		
		$app = JFactory::getApplication();

		$model = $this->getModel('login');
		$credentials = $model->getState('credentials');
		$return = $model->getState('return');

		$result = $app->login($credentials, array('action' => 'core.login.admin'));

		if (!($result instanceof Exception))
		{
			$app->redirect($return);
		}

		parent::display();
	}
}
