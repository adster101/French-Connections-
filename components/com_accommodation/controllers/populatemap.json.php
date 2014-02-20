<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * Suggestions JSON controller for Finder.
 *
 * @package     Joomla.Site
 * @subpackage  com_finder
 * @since       2.5
 */
class AccommodationControllerPopulateMap extends JControllerLegacy {
  
	public function getItems($cachable = false, $urlparams = false)
	{
    
    JRequest::checkToken('GET');
    
    $input = JFactory::getApplication()->input;
    $lat = $input->get('lat','','float');
    $lon = $input->get('lon','','float');

    // Get the suggestions.
    $model = $this->getModel('Listing', 'AccommodationModel');
    $return = $model->getMapItems($lat, $lon);

		// Send the response.
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
}
