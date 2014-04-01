<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class RentalControllerHelloWorld extends JControllerLegacy {

  /**
	 * Method to find the properties assigned to a users account.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function PropertyList($cachable = false, $urlparams = false)
	{
		$return = array();
		
		$model = $this->getModel('PropertyList', 'RentalModel');
		$return = $model->getItems();

		// Check the data.
		if (empty($return))
		{
			$return = array();
		}

		// Use the correct json mime-type
		header('Content-Type: application/json');

		// Send the response.
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
  
  /**
	 * Method to find the properties assigned to a users account.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function NearestPropertyList($cachable = false, $urlparams = false)
	{
		$return = array();
		
    $input = JFactory::getApplication()->input;

    $latitude = $input->get('lat','','string');
    $longitude = $input->get('lon','','string'); 
    
		$model = $this->getModel('NearestPropertyList', 'RentalModel',
            $config = array('latitude'=>$latitude, 'longitude'=>$longitude));
    
		$return = $model->getItems();

		// Check the data.
		if (empty($return))
		{
			$return = array();
		}

		// Use the correct json mime-type
		header('Content-Type: application/json');

		// Send the response.
		echo json_encode($return);
		JFactory::getApplication()->close();
	}  
}
