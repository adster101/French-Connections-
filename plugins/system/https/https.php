<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.sef
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Joomla! SEF Plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  System.sef
 * @since       1.5
 */
class PlgSystemHTTPS extends JPlugin
{
	/**
	 * 301 redirect http requests to https 
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		if ($app->getName() != 'site' || $doc->getType() !== 'html')
		{
			return;
		}

    $uri = JURI::getInstance();

		if ($uri->getScheme() != 'https')
		{
			// Forward to https
			$uri->setScheme('https');
			$app->redirect((string) $uri, true);
		}
	}
}
